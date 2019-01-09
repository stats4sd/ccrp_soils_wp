const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');
const csv=require('csvtojson')
const request=require('request');
const d3 = require('d3-dsv');
const mysql = require('mysql');
const math = require('mathjs');
const iconv = require('iconv-js');
const config = require('./config');
const JSON = require('JSON');
const lodash = require('lodash');
const XLSX = require('xlsx');
const xlsx = require('node-xlsx');

var connection = mysql.createConnection({
    host: "localhost",
    user: config.config.username,
    password: config.config.password,
    database: "soil"
});

var pool = mysql.createPool({
    host: "localhost",
    user: config.config.username,
    password: config.config.password,
    database: "soil"
});

//Upload xlsx file in soil database

async function main(){

    const path = "C:/Users/LuciaFalcinelli/Documents/GitHub/soiltool/ccrp_soils-Aggregates_EN_ES_20181221_0810GMT.xlsx";
    var workbook = XLSX.readFile(path);

    //takes title of the form from file xlsx 
    var worksheet = workbook.Sheets['settings'];
    var cell = worksheet['A2'];
    let titleFile = cell.v;

    //converts datas to json format
    function to_json(workbook){
        var result = {};
        workbook.SheetNames.forEach(function(sheetName){
            var roa = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
            if(roa.length > 0){
                result[sheetName] = roa;
            }
        });
        return result;
    };

    parsedData = to_json(workbook);

    //Checks if the version is new or not from form title.
    if(!getFormIDByName){
        pool.getConnection((err, connection) =>{
            if (err) throw err;

            parsedData['settings'] = parsedData['settings'].map((item, index) =>{
                var newItem = {};
                newItem['form_title'] = item['form_title'];
                newItem['form_id'] = item['form_id'];
                newItem['version'] = item['version'];
                newItem['instance_name'] = item['instance_name'];
                newItem['default_language'] = 'english';
       
            insertToTableForms(connection, newItem, processResult);
            }); 
        });

        async function insertToTableForms(conncetion, newItem, callback){
            connection.query("INSERT INTO `xls_forms` SET ?;", newItem, function (err, result, fields){
                if (err) {
                    console.log("err",err);
                }
                else {
                    callback(null,result);
                }
            });
            }
    }

    //Cleaning name columns from space white, uppercase and accents
    function clean(object){
        Object.keys(object).forEach(function(key){
            var newkey = key.trim()
                newkey = newkey.toLowerCase();
                newkey = lodash.deburr(newkey);
                if(key !== newkey){
                    object[newkey] = object[key];
                    delete object[key];
                };
        });
    };

    
    parsedData['survey'].forEach(function(row){
        clean(row);
    });

    //ADD HERE A CODE TO UPLOAD CHOICES TO DATABASE 
    // parsedData['choices'].forEach(function(row){
    //     clean(row);
    // });

    //Identify the form in database return form_id
    async function query(sql){
        return new Promise((resolve,reject)=>{
            connection.query(sql, function(err, result, fields){
                if(err){
                    //connection.end()
                    return reject(err)
                }
               // connection.end()
                resolve(result)
            });
        });
    };

    async function corrispForm(connection){
        const forms = await query("SELECT * FROM xls_forms;")
        const id = getFormIDByName(forms)
        return id
    };

    //Checks if the form title is in the database and return form id and true if exists.
    function getFormIDByName(forms){
        const matchingForms = forms.filter( (form, index) => {
            return titleFile == form["form_title"];
        });
        //console.log('matching form',matchingForms[0].id)
        return matchingForms[0].id
    };  

    //Delete the old version of the form from form_id. 
    async function deleteformdb(){
        const id = await corrispForm(connection)
        const delResponse = await query("DELETE FROM xls_form_questions WHERE form_id="+ id +";")
        return id;
    }

    let form_id = await deleteformdb();
    
    //Adds form_id column in parsedData.
    parsedData['survey'] = parsedData['survey'].map( (item, index) => {      
        item["form_id"] = form_id;
        return item
    })
   
    //Uploads datas in the corrisponding columns 
    pool.getConnection((err, connection) => {
        if (err) throw err;
        parsedData['survey'] = parsedData['survey'].map( (item, index) => {
            var newItem = {}
            newItem['type'] = item['type'];
            newItem['name'] = item['name'];
            newItem['hint::english'] = item['hint::english'];
            newItem['relevant'] =item['relevant'];
            newItem['constraint'] = item['constraint']; 
            newItem['constraint_message::english'] = item['constraint_message::english'];
            newItem['required'] = item['required'];
            newItem['required_message::english'] = item['required_message::english'];
            newItem['appearance'] = item['appearance'];
            newItem['default'] = item['default'];
            newItem['calculation'] = item['calculation'];
            newItem['count'] = item['count'];
            newItem['label::english'] = item['label::english'];
            newItem['form_id'] = item['form_id'];
            newItem['label::espanol'] = item['label::espanol'];
            newItem['hint::espanol'] = item['hint::espanol'];
            newItem['label'] = item['label'];
            newItem['hint'] = item['hint'];
  
        insertToTable(connection, newItem, processResult);
        });
    });   

    async function insertToTable(conncetion, newItem, callback){
        connection.query("INSERT INTO `xls_form_questions` SET ?;", newItem, function (err, result, fields){
        if (err) {
            console.log("err",err);
        }
        else {
            callback(null,result);
        }
    });
    }

    function processResult(err,result){
    console.log(result)
    }

}
main();
