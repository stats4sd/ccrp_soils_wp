var editor;
var projectFormsTable = [];
var questions;
var choices;
jQuery(document).ready(function($){

  console.log("current user", vars.user_group_ids);

  // Setup main forms table(s);
  setup_project_forms_table();


  // Get questions and choices to speed up form creation
  questionGet = getData(vars,"dt_xls_form_questions")
  .done(function(response){
    //reformat response data to just get the question objects;
    questions = response.data.map(function(item,index){
      return item.xls_form_questions;
    })
  })
  // in case of failure;
  .fail(function(){
    questions = 'error';
    console.log("could not get xls form questions");
  })

  choicesGet = getData(vars,"dt_xls_form_choices")
  .done(function(response){
    choices = response.data.map(function(item,index){
      return item.xls_form_choices;
    })
  })
  .fail(function(){
    questions = 'error';
    console.log("could not get xls form choices");
  })

}); //end doc ready;


function deploy_form(table_id,id){
  var form = {};

  //get row data;
  var data = projectFormsTable[table_id].rows(id).data().toArray();
  var recordId = data[0].project_forms_info.id;
  var form_type_id = data[0].project_forms_info.form_id;
  console.log(data)

  //take form_id and get build form:...
  form.survey = prepare_survey(form_type_id);
  form.choices = prepare_choices(form.survey);
  form.settings = prepare_settings(data[0]);

  console.log(form);

  // Add form name (for XLS form builder in Node app)
  form.name = form.settings.form_title

  //send the form off to node:
  //// Node app will then create the XLS form from the form JSON object, and publish it to the soils_ccrp Kobotoolbox account.
  jQuery.ajax({
    url: vars.node_url + "/customDeployForm",
    method: "POST",
    dataType: "json",
    contentType: "application/json; charset=utf-8",
    data: JSON.stringify(form)
  })
  .done(function(response){
    console.log("success",response);

    //checking if the response has an API url to to the form on Kobo works as another check of success.
    if(response.msg.url){
      
      form.kobo_id = response.msg.formid;

      //save kobo_id to the projects_forms tabls for later reference;
      jQuery.ajax({
        url: vars.ajax_url,
        method: "POST",
        data: {
          kobo_id: form.kobo_id,
          id: recordId,
          action: "kobo_form_save_id",
          secure: vars.nonce
        }
      })
      .done(function(response){
        console.log("response from kobo_id db update:",response);
        
        //reload table data;
        projectFormsTable[table_id].ajax.reload();
      })
      .fail(function(response){
        console.log("fail from kobo_id db update",response);
        //reload table data;
        projectFormsTable[table_id].ajax.reload();
      })
        
      //take project_kobo_account, then add sharing permissions via Kobo API.
      kobotools_account = data[0].project_forms_info.project_kobotools_account;

      //prepare json object for sharing API call
      shareBody = {};
      shareBody.form_id = form.kobo_id;
      shareBody.username = kobotools_account;
      shareBody.role = "manager";

      // Call Node App to share form
      jQuery.ajax({
        url: vars.node_url + "/shareForm",
        method: "POST",
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(shareBody),
        success: function(response){
          console.log("response from sharing = ",response);
        },
        error: function(response){
          console.log("error from sharing = ",response);
        }
      }) //end sharing requiest


    }
    else {
      
      // display ODK error in console.
      text = "ODK error: " + response.msg.text;
      console.log("error, ", response.msg.text)

    }
  })
  .fail(function(response){
    console.log("error",response);
  })

}


//returns json object for the survey sheet.
function prepare_survey(form_type_id) {

  var survey = [];
  survey = questions.filter(function(item,index){
    
    // return every question for the required form type.
    if(item.form_id == form_type_id){
      return true;
    }
    return false;
  })

  return survey;
}

//function takes list of questions and prepares the choices sheet to include all required choice lists;
//returns json object for the choices sheet
function prepare_choices(questions) {

  // Matches lookups from 'select_one [choices]' and 'select_multiple [choices]'
  // Always include the placeholder choice, so the sheet headers are always rendered.
  var choicesTracker = ['na'];


  //go through every question in survey:
  questions.forEach(function(question,index){

    // match select questions, and track the second term (choices reference)
    if(question.type.indexOf("select") > -1) {

      //split select by the space to get just the choices list name:
      var meta = question.type.split(" ")

      //add the choices list name to the choicesTracker with a value of true
      choicesTracker.push(meta[1]);
    }
  })

  //go through all the choices, and if their list_name matches one of the choicesTracker items, add it to selectedChoices.
    var selectedChoices = jQuery.map(choices,function(item,index){
    if(choicesTracker.some(i => i == item.list_name)) {
      return item;
    }
  })

  return selectedChoices;
}

//returns json object for settings sheet
function prepare_settings(data){

  settings = [data.xls_forms];

  //get and format the project name ready to add to form id
  pName = data.project_forms_info.project_name.toLowerCase();
  pName = pName.replace(/\s/g,"-")
  
  //prepend project ID to form ID and title.
  settings[0].form_id = pName + "_" + settings[0].form_id;
  settings[0].form_title = pName + " - " + settings[0].form_title;

  //Apply testing string to allow for multiples...
  settings[0].form_id += "_test_" + Math.floor((Math.random() * 100000) + 1).toString()

  return settings;
}

function downloaddata(project){
  console.log(project)

  vars.project_id = project;
  soilData = getData(vars,'dt_soils')
  .done(function(response){
    response = response.body;
    console.log(response);
  })
}


function setup_project_forms_table() {
    //Setup datatables columns:


  project_list = vars.user_groups;

  project_list.forEach(function(project){

    var projectFormsColumns = [
      {data: "project_forms_info.id", title: "ID", visible: false},
      {data: "project_forms_info.project_id", title: "Project ID", visible: false},
      {data: "project_forms_info.project_name", title: "Project Name", visible: false},
      {data: "project_forms_info.project_kobotools_account", title: "Project Name", visible: false},
      {data: "project_forms_info.project_slug", title: "Project Slug", visible: false},
      {data: "project_forms_info.form_id", title: "Form ID", visible: false},
      {data: "xls_forms.form_title", title: "Form Name", visible: true, width:"40%"},
      {data: "project_forms_info.form_kobo_id", title: "Kobotools Form ID", visible: true, width:"20%"},
      {data: "project_forms_info.count_records", title: "Number of Collected Records", visible: true, witdh:"5%"},
      {data: "project_forms_info.project_kobotools_account", title: "Status",visible:true, render: function(data,type,row,meta){
        console.log("row = ",row);

        //if no kobotools account is defined, direct user to add one:
        if(data == "" || data == null){
          
        }

        //if form is deployed, suggest updating locations csv file
        if(row.project_forms_info.form_kobo_id != null && row.project_forms_info.form_kobo_id != ""){

          // //only offer locations update for intake form:
          // if(row.project_forms_info.form_id == 1){
          //   return "<button class='btn btn-link' onclick='update_locations("+meta.row+")'>update locations csv</button'"
          // }
          return "form deployed"
        }

        return "<button class='btn btn-link' onclick='deploy_form("+project.id+","+meta.row+")'>deploy</button>";
      }},
    ];

    console.log("project",project);

    vars.project_id = project.id;
    project_id = project.id
    //datatables parameters
    projectFormsParams = {
      vars: vars,
      action: "dt_project_forms",
      target: "forms_table_" + project.id,
      columns: projectFormsColumns,
      options: {
        dom: "tpB",
        select: false,
        pageLength: 150,
        buttons: [
        {
            text: "Pull new records from Kobotoolbox",
            action: function(e,dt,node,config){
              update_counts(dt);
            }
          },
        ],
      }
      }

    //call datatables function
    table = newDatatable(projectFormsParams);

    projectFormsTable[project_id] = table;

  })


}

/*************** COPIED FROM NRC *********************/

////// Functions to update form record counts and pull new records from KOBO into the MySQL database:
///
function update_counts(dt){
  //get list of forms for current view
  console.log("checking Kobotoolbox for new submissions");
  console.log(dt.column(0).data());

  forms = dt.data().toArray();


  var requests = [];

  //for Each formId, setup request, then push to promises;
  forms.forEach(function(form,index){
    formId = form.project_forms_info.form_kobo_id;

    console.log(index,formId);
      // return false;


    existingIds = form.project_forms_info.id_list
    console.log("existingIds = ",existingIds)
    if(existingIds){
      existingIds = existingIds.split(",");
      console.log("splitIds = ",existingIds)
    }

    //split into array:


    if(formId != null) {

      request = requestFormCount(formId);
      requests.push(request);

      request.done(function(response){
        
        if(response.statusCode != 200) {
          throw("warning - unable to reach Kobotoolbox site to retrieve new data. Please check that the Kobotoolbox site is currently accessible through the browser");
        }
        formCountResponse(dt,response,form)
      });
    }


  });


  jQuery.when.apply(jQuery,requests).then(function(responses){
    console.log();
    user_alert("All forms in current view checked and syncronised","info","alert-space");
  })
}

function requestFormCount(formId) {
  request = jQuery.ajax({
    url: vars.node_url + "/countRecords",
    method: "POST",
    dataType: "json",
    contentType: "application/json; charset=utf-8",
    data: JSON.stringify({
      kobo_id: formId
    })
  })

  return request;
}

function formCountResponse(dt,response,form,existing_ids){
  db_count = form.project_forms_info.count_records
  console.log("db count = ",db_count)
  if(db_count == null){
    db_count = 0;
  }
  console.log("function response for",response);
  //check count against count:
  if(db_count == response.count){
    console.log("kobo_form with id" + response.kobo_id + " has same number of records as database")
    return;
  }

  if(db_count > response.count){
    console.log("kobo_form with id" + response.kobo_id + "has fewer records than database")
    return;
  }

  if(db_count < response.count){
    console.log("kobo_form with id" + response.kobo_id + " has more records - starting to pull them");
    console.log("getting data but not existing Ids = ",form.project_forms_info.id_list);
    existing_ids = form.project_forms_info.id_list;
    if(existing_ids){
      existing_ids = existing_ids.split(",");
    }

    pullRequest = jQuery.ajax({
      url: vars.node_url + "/pullData",
      method: "POST",
      dataType: "json",
      contentType: "application/json; charset=utf-8",
      data: JSON.stringify({
        kobo_ids: [response.kobo_id],
        existing_ids: existing_ids
      })
    })

    pullRequest.done(function(response){
      console.log("data pulled");
      //add pulled data to collected_data table:
      response = response.body;
      console.log("response, ",response);

      savedata = {};
      response.forEach(function(row,index){
        

        //insert functions from original node JS to parse into main tables:
        parse_data_into_tables(row,form);


        record = JSON.stringify(row);
        
        //remove / from keys:
        record = record.replace(/\//g,"_");
        //console.log("record:",record)
        savedata[index] = {};
        savedata[index]["DT_RowId"] = index;
        savedata[index]["xls_form_submissions"] = {
          form_kobo_id: row._form_kobo_id,
          record_data: record,
          uuid: row._uuid
        }
      })

      console.log("would save",savedata);
      //forms_table.ajax.reload().draw()

      jQuery.ajax({
        url: vars.ajax_url,
        dataType: "json",
        method:"POST",
        data:{
          action:"dt_xls_form_submissions",
          secure:vars.nonce,
          dt_action:"create",
          data: savedata
        }
      })
      .done(function(response){
        console.log("response from db: ",response);
        // soils_table.ajax.reload();
        dt.ajax.reload();
      })

    })
    console.log();
    user_alert("New records successfully pulled from Kobo","success","alert-space");
    return;
  }
}


function parse_data_into_tables(data,form){
  console.log("form here is",form);
  formType = form.xls_forms.form_id;
  projectId = form.project_forms_info.project_id
  

  //work out which form type it is...
  if(formType == "ccrp_soil_intake"){
    // add inserts for community; farmer; plot etc...


    // *****************************************************
    // Insert Samples
    // *****************************************************
    var sampleValues = {};


    //for each sample within this plot...
    for (var y = 0; y < data['sample_info'].length; y++) {
      //wrap inside self-serving function to avoid async issues with x and ys ...
      console.log("sample ID = ",data['sample_info'][y]['sample_info/sample_id']);
      console.log("farmer ID = ",data['farm_id']);

      sampleDate = data['sample_info'][y]['sample_info/sampling_date'];
      console.log("sampleDate = ",sampleDate);
      console.log(typeof sampleDate);
      dateLength = sampleDate.length;
      if(dateLength > 9){
        saempleDate = sampleDate.substring(0,10);
      }

        (function(y) {
          sampleValues[y] = {};
          sampleValues[y]["Dt_RowId"] = y;
          sampleValues[y]["samples"] = {
            id: data['sample_info'][y]['sample_info/sample_id'],
            farmer_id: data['farm_id'],
            //plot_name: data['plot_name'],
            //plot_gradient: data['plot_gradient'],
            //farmer_kn_soil: data['farmer_knowledge_soil_type'],
            soil_texture: data['sample_info'][y]['sample_info/soil_texture'],
            sampling_date: sampleDate,
            sampling_depth: data['sample_info'][y]['sample_info/sampling_depth'],
            sample_comments: data['sample_info'][y]['sample_info/sample_comments'],
            collector_name: data['_submitted_by'],
            project_id: projectId
          }
       })(y); //end function(y);
    } //end for loop to go around the samples

    //insert Sample values into Db via editor ajax function:
    jQuery.ajax({
      url: vars.ajax_url,
      dataType: "json",
      method:"POST",
      data:{
        action:"dt_samples",
        secure:vars.nonce,
        dt_action:"create",
        data: sampleValues
      }
    })
    .done(function(response){
      console.log("response from inserting samples to db: ",response);
    })



  } 

  if(formType == "ccrp_soil_p") {
    p = {};
    p[0] = {};
    p[0]["Dt_RowId"] = 0;
    p[0]["analysis_p"] = {
      sample_id: data['sample_id'],
      analysis_date: data['analysis_date'],
      weight_soil: data['weight_soil'],
      vol_extract: data['vol_extract'],
      vol_topup: data['vol_topup'],
      color: data['color'],
      cloudy: data['cloudy'],
      raw_conc: data['Raw_conc'],
      olsen_p: data['olsen_p'],
      blank_water: data['blank_water'],
      correct_moisture: data['correct_moisture'],
      moisture: data['moisture'],
      olsen_p_corrected: data['olsen_p_corrected']

    }

    console.log("final p",p);

    jQuery.ajax({
      url: vars.ajax_url,
      dataType: "json",
      method:"POST",
      data:{
        action:"dt_analysis_p",
        secure:vars.nonce,
        dt_action:"create",
        data: p
      }
    })
    .done(function(response){
      console.log("response from inserting p to db: ",response);
    })

  }

    if(formType == "ccrp_soil_ph") {
    ph = {};
    ph[0] = {};
    ph[0]["Dt_RowId"] = 0;
    ph[0]["analysis_ph"] = {
      sample_id: data['sample_id'],
      analysis_date: data['analysis_date'],
      weight_soil: data['weight_soil'],
      vol_water: data['vol_water'],
      reading_ph: data['reading_ph'],
      stability: data['stability']
    }

    console.log("ph",ph);

    jQuery.ajax({
      url: vars.ajax_url,
      dataType: "json",
      method:"POST",
      data:{
        action:"dt_analysis_ph",
        secure:vars.nonce,
        dt_action:"create",
        data: ph
      }
    })
    .done(function(response){
      console.log("response from inserting ph to db: ",response);
    })

  }

    if(formType == "ccrp_soil_poxc") {
    poxc = {};
    poxc[0] = {};

    if(data.hasOwnProperty('moisture')){
        if(data.estimated_soilmoisture != null && data.estimated_soilmoisture != 0 && data.estimated_soilmoisture != "") {
            soil_moisture = data.estimated_soilmoisture;
        }
        else {
            soil_moisture = 0;
        }
    }
    else {
        soil_moisture = 0;
    }

    poxc[0]["Dt_RowId"] = 0;
    poxc[0]["analysis_poxc"] = {
      sample_id: data['sample_id'],
      analysis_date: data['analysis_date'],
      weight_soil: data['weight_soil'],
      color: data['color'],
      color100: data['color100'],
      conc_digest: data['conc_digest'],
      cloudy: data['cloudy'],
      colorimeter: data['colorimeter'],
      raw_conc: data['raw_conc'],
      poxc_sample: data['poxc_sample'],
      posx_soil: data['posx_soil'],
      correct_moisture: data['correct_moisture'],
      moisture: soil_moisture,
      poxc_soil_corrected: data['poxc_soil_corrected']
    }

    jQuery.ajax({
      url: vars.ajax_url,
      dataType: "json",
      method:"POST",
      data:{
        action:"dt_analysis_poxc",
        secure:vars.nonce,
        dt_action:"create",
        data: poxc
      }
    })
    .done(function(response){
      console.log("response from inserting poxc to db: ",response);
    })

  }
}