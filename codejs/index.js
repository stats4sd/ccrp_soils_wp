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



var connection = mysql.createConnection({
    host: "localhost",
    user: config.config.username,
    password: config.config.password,
    database: "soiS"
});

var pool = mysql.createPool({
    host: "localhost",
    user: config.config.username,
    password: config.config.password,
    database: "soilS"
});


async function read(path) {
    return new Promise((resolve, reject) => {
        fs.readFile(path, "utf8", (err, data) => { // utf16le or utf8
            if (err){
                throw err;
            }
            resolve(data);
        });
    });
}

// Function for writing files
// Requires:
//  - path: path to file to write (string)
//  - content: the contents of the file (string)
async function write(path,content) {
    return new Promise((resolve,reject) => {
        fs.writeFile(path,content, (err) => {
            if (err) {
                throw err;
            }
            console.log("file saved to path ", path);
        })
    })
}


//upload questions from form excel.csv


async function main(){


    const path = "C:/Users/LuciaFalcinelli/Documents/GitHub/soiltool/ccrp_soils-Aggregates_EN_ES_20181221_0810GMT.csv";
    let rawData = await read(path, "utf8");
    let parsedData = d3.csvParse(rawData);
    let col_csv = Object.keys(parsedData[0]);
    let titleFile = [];

    // CLEANING name columns FROM SPACE WHITE, UPPERCASE AND ACCENTS 

    function clean(object){
        Object.keys(object).forEach(function(key){
            var newkey = key.trim()
            newkey = newkey.toLowerCase();
            newkey = lodash.deburr(newkey);
            if(key !== newkey){
                object[newkey] = object[key];
                delete object[key];
            }
        })
    }
  
    parsedData.forEach(function(row){
    // row == parsedData[2]
        clean(row)
    })

    console.log(parsedData)

    // changes the word espanol with spanish 

    async function corrispName(col_csv){

        for(var i = 0; i <= col_csv.length -1 ; i++){          
            var start = ((col_csv[i].length) - ("espanol".length))
            var end = col_csv[i].length
            if(col_csv[i].substring(start,end) == "espanol"){
                col_csv[i] = col_csv[i].substring(0,start)+"spanish"
            }
        }
    }
    



    // title of the form in the file 
    // parsedData.forEach( (item, index) => {      
    //     const titleCol = item["form_title"];
    //     if(titleCol !=""){
    //         titleFile = titleCol;
    //     }
        
      
    // });


    // function finds the corrispondence between name of columns from csv file and database and return true and false //position
    
    async function corrispColumns(connection, name_table){
        
        connection.query("SELECT * FROM "+ name_table +";", function(err, result, fields){

            let col_db =Object.keys(result[0]);
            console.log(col_db)
      
            for (var i = 0; i <= col_csv.length -1 ; i++) {
                for (var j = 0; j <= col_db.length -1 ; j++) {
                    if(col_csv[i] == col_db[j]){
                       
                        console.log("["+ i +"]["+ j +"]");
                        console.log(col_csv[i],col_db[j])

                        }
                }  
            }
       

        });     
      


    }   
    //corrispColumns(connection, "xls_form_questions")

    // Identify the form in database return form id
    //titleFile
    

    async function query(sql){
        return new Promise((resolve,reject)=>{
             connection.query(sql, function(err, result, fields){
                if(err){
                    //connection.end()
                    return reject(err)
                }
               // connection.end()
                resolve(result)
        })
        })
    }

    async function corrispForm(connection){
        //console.log('start of corrispForm')
        const forms = await query("SELECT * FROM xls_forms;")
        const id = getFormIDByName(forms)
        return id
    }

    function getFormIDByName(forms){
        const matchingForms = forms.filter( (form, index) => {
            return titleFile == form["form_title"];
        })
        //console.log('matching form',matchingForms[0])
        //return matchingForms[0].id
    }  

    async function deleteformdb(){

        const id = await corrispForm(connection)
        const delResponse = await query("DELETE FROM xls_form_questions WHERE form_id="+5+";")
        return id;
   

    }
   //deleteformdb()


    //et form_id = await deleteformdb();

    parsedData = parsedData.map( (item, index) => {      
        item["form_id"] = 5;
        return item
    })
    console.log(parsedData)

   


    // create a id_form column with id from database

     // parsedData = parsedData.map((item, index)=>{
     //     item["form_id"] = form_id
     //     return item

     // })

    // console.log(parsedData)


    

      pool.getConnection((err, connection) => {

        if (err) throw err;

        parsedData = parsedData.map( (item, index) => {
            var newItem = {}
            newItem['type'] = item['type']
            newItem['name'] = item['name']
            newItem['hint::english'] = item['hint::english']
            newItem['relevant'] =item['relevant']
            newItem['constraint'] = item['constraint'] 
            newItem['constraint_message::english'] = item['constraint_message::english']
            newItem['required'] = item['required'];
            newItem['required_message::english'] = item['required_message::english'];
            newItem['appearance'] = item['appearance'];
            newItem['default'] = item['default'];
            newItem['calculation'] = item['calculation'];
            newItem['count'] = item['count'];
            newItem['label::english'] = item['label::english'];
            newItem['form_id'] = item['form_id'];
            newItem['label::espanol'] = item['constraint_message::espanol'];
            newItem['hint::espanol'] = item['hint::espanol'];
            newItem['label'] = item['label'];
            newItem['hint'] = item['hint'];

      
        insertToTable(connection, newItem, processResult)

    })
    })   


    //corrispColumns(connection, "xls_form_questions")

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






    // //Insert rowns in data base 
    // async function selectColumns(connection, name_columns){
    //     connection.query("SELECT "+name_columns +" FROM xls_form_questions;", function(err, result, fields){


    //         if(err){
    //             console.log("err", err);
    //         }
    //         else{
    //             console.log("result", result);
    //         }
    //     });
    // }



    // function to check if the matching exists
    //connection.query("SELECT "+'*'+" FROM xls_form_questions;", function(err, result, fields){

        //One row result[0], all rows result,  
        
        //var element = Object.values(JSON.parse(JSON.stringify(result)));

       
        
     //  console.log(element[1])


//    });


    //selectColumns(connection, 'type')
}


main();