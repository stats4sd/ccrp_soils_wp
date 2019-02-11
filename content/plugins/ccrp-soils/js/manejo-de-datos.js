var editor;
var projectFormsTable = [];
var questions;
var choices;

// temp langauge fix. Should refactor:

function getString(num){
  if(vars.lang == "en"){
    switch(num){
      case 1: return "deploying form to kobotoolbox account";
      case 2: return "generating XLS Form";
      case 3: return "Sending form data to Kobotoolbox";
      case 4: return "form successfully added to Kobotoolbox";
      case 5: return "sharing with project account";
      case 6: return "The new form has been shared with your project's Kobotoolbox account";
      case 7: return "You can now access the form via Kobotoolbox / ODK-Collect.";
      case 8: return "The new form was created and added to Kobotoolbox, but could not be shared with your project's Kobotoolbox account";
      case 9: return "Please check that you have entered the correct kobotools username in your project settings.";
      case 10: return "The new form could not be created. Please share the following ODK error message with support@stats4sd.org:";
      case 11: return "The Kobotoolbox API could not be contacted. Please check your connection settings and try again. If this issue persists, please contact support@stats4sd.org";
      case 12: return "deploy";
      case 13: return "delete form";
      case 14: return "Sync data from Kobotoolbox";
      case 15: return "Download Data";
      case 17: return "not depoyed";
      case 18: return "deployed";
      case 19: return "Form Name";
      case 20: return "Kobotools Form ID";
      case 21: return "Number of Collected Records";
      case 22: return "Status";
      case 23: return "Action";
      case 24: return "All deployed forms checked";
      case 25: return "Update Form";
    }
  }

  if(vars.lang == "es"){
    switch(num){
      case 1: return "desplegando la formulario";
      case 2: return "generando la formulario";
      case 3: return "Sending form data to Kobotoolbox";
      case 4: return "Formulario añadido a kobotoolbox";
      case 5: return "sharing with project account";
      case 6: return "The new form has been shared with your project's Kobotoolbox account";
      case 7: return "You can now access the form via Kobotoolbox / ODK-Collect.";
      case 8: return "The new form was created and added to Kobotoolbox, but could not be shared with your project's Kobotoolbox account";
      case 9: return "Please check that you have entered the correct kobotools username in your project settings.";
      case 10: return "The new form could not be created. Please share the following ODK error message with support@stats4sd.org:";
      case 11: return "The Kobotoolbox API could not be contacted. Please check your connection settings and try again. If this issue persists, please contact support@stats4sd.org";
      case 12: return "desplegar";
      case 13: return "eliminar formulario";
      case 14: return "Sincronizar los datos de kobotoolbox";
      case 15: return "Descargar datos";
      case 17: return "no desplegado";
      case 18: return "desplegado";
      case 19: return "Nombre del formulario";
      case 20: return "ID del formulario en Kobotools";
      case 21: return "Número de registros recopilados";
      case 22: return "Estado";
      case 23: return "Acción";
      case 24: return "Todas las formas desplegadas verificadas";
      case 25: return "Actualiza el formulario";
    }
  }
}

jQuery(document).ready(function($){


  console.log("LANGUAGE = ", vars.lang);

  console.log("current user", vars.user_group_ids);

  // Setup main forms table(s);
  setup_project_forms_table();


  choicesGet = getData(vars,"dt_xls_form_choices")
  .done(function(response){
    choices = response.data.map(function(item,index){
      return item.xls_form_choices;
    })

    console.log("choices GOT",choices);
  })
  .fail(function(){
    questions = 'error';
    console.log("could not get xls form choices");
  })

  // Get questions and choices to speed up form creation
  questionGet = getData(vars,"dt_xls_form_questions")
  .done(function(response){

    console.log("question response",response);
    if(!response) {
      user_alert("warning; cannot retrieve questions for XLS forms. The form creation will likely not work.","warning");
      return ;
    }

    //reformat response data to just get the question objects;
    questions = response.data.map(function(item,index){
      return item.xls_form_questions;
    })
    console.log("questions GOT",questions);
  })
  // in case of failure;
  .fail(function(){
    questions = 'error';
    console.log("could not get xls form questions");
  })

}); //end doc ready;

function update_form(table_id,id){
  working("redeploying form to kobotoolbox account");

  var form = {};

  //get row data;
  var data = projectFormsTable[table_id].rows(id).data().toArray();
  var recordId = data[0].project_forms_info.id;
  var form_type_id = data[0].project_forms_info.form_id;

  console.log(data)

  working("generating XLS Form");

  //take form_id and get build form:...
  form.survey = prepare_survey(form_type_id);
  form.choices = prepare_choices(form.survey,form_type_id);
  form.settings = prepare_settings(data[0]);

  console.log(form);

  working("Sending form data to Kobotoolbox");
  // Add form name (for XLS form builder in Node app)
  form.name = form.settings.form_title;
  form.kobo_id = data[0].project_forms_info.form_kobo_id;

  jQuery.ajax({
    url: vars.node_url + "/customUpdateForm",
    method: "POST",
    dataType: "json",
    contentType: "application/json; charset=utf-8",
    data: JSON.stringify(form)
  })
  .done(function(response){
    console.log("success",response);

    if(response.msg.url){
      user_alert("form successfully updated in Kobotoolbox","info");
      working();
    }
  })
  .fail(function(response){
    user_alert("Failed to update form on kobotools with ID " + form.kobo_id + "Please screenshot this message and send it to support@stats4sd.org: " + response);
    working();
  })

}


function deploy_form(table_id,id){
  working(getString(1));

  var form = {};

  //get row data;
  var data = projectFormsTable[table_id].rows(id).data().toArray();
  var recordId = data[0].project_forms_info.id;
  var form_type_id = data[0].project_forms_info.form_id;


  console.log(data)

  working(getString(2));

  //take form_id and get build form:...
  form.survey = prepare_survey(form_type_id);
  form.choices = prepare_choices(form.survey,form_type_id);
  form.settings = prepare_settings(data[0]);

  console.log(form);

  working(getString(3));
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

      user_alert(getString(4),"info");
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

      working(getString(5) + " ( " + kobotools_account + " ) ")

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
          working("sharing success");
          user_alert(getString(6) +  " ( "+kobotools_account+" )" + getString(7) ,"success");
          console.log("response from sharing = ",response);
          working();
        },
        // #######################################################################################################################################
        error: function(response){
          working("sharing error");
          user_alert(getString(8) + " ( "+kobotools_account+" ) " + getString(9))
          console.log("error from sharing = ",response);
          working();
        }
      }) //end sharing requiest


    }
    else {

      // display ODK error in console.
      text = "ODK error: " + response.msg.text;
      console.log("error, ", response.msg.text)
      user_alert(getString(10) + text,"danger");
      working();
    }
  })
  .fail(function(response){
    user_alert(getString(11),"danger");
    working();
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
function prepare_choices(questions,form_type_id) {

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
    var selectedChoices = choices.map(function(choicesItem,index){
      choicesTracker.forEach(function(choiceTrackerItem,index){
        if(choicesItem.list_name == choicesTracker && choicesItem.form_id == form_type_id){
          return choicesItem;
        }
      })
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

function downloaddata(project_id){
  console.log(project_id)

  vars.project_id = project_id;
  soilData = getData(vars,'dt_soils')
  .done(function(response){
    console.log(response);

    data = response.data;

    data = data.map(function(item,index){
      thing = item.samples_merged;
      return thing;
    })

    if(data.length > 0 ){


    console.log("final data output",data);

    var headers = Object.keys(data[0]);
    var timeNow = new Date();
    var fileName = "_soil_sample_data_download_"+ date_iso(timeNow,"datetime");
    exportCSVFile(headers,data,fileName);
    }
    else {
      alert("Lo sentimos mucho pero no hay datos que descargar de la base de datos")
    }
  })
}


function setup_project_forms_table() {

  project_list = vars.user_groups;

  // prepare a table for each project.
  project_list.forEach(function(project){

    var projectFormsColumns = [
      {data: "project_forms_info.id", title: "ID", visible: false},
      {data: "project_forms_info.project_id", title: "Project ID", visible: false},
      {data: "project_forms_info.project_name", title: "Project Name", visible: false},
      {data: "project_forms_info.project_kobotools_account", title: "Project Name", visible: false},
      {data: "project_forms_info.project_slug", title: "Project Slug", visible: false},
      {data: "project_forms_info.form_id", title: "Form ID", visible: false},
      {data: "xls_forms.form_title", title: getString(19), visible: true, width:"40%"},
      {data: "project_forms_info.form_kobo_id", title: getString(20), visible: true, width:"20%"},
      {data: "project_forms_info.count_records", title: getString(21), visible: true, witdh:"10%"},
      {data: "project_forms_info.form_kobo_id", title: getString(22),visible:true, render: function(data,type,row,meta){
        console.log("row = ",row);

        if(data === null || data === ""){
          return getString(17)
        }

        //if form is deployed, suggest updating locations csv file
        if(data != null && data != ""){
          return getString(18)
        }

      }},
      {data: "project_forms_info.form_kobo_id", title: getString(23), visible: true, width: "10%", render: function(data,type,row,meta){

        //if not deployed, render 'deploy' button;
        if(data === null || data === ""){
          return "<button class='btn btn-link submit_button' onclick='deploy_form("+project.id+","+meta.row+")'>"+getString(12)+"</button>";
        }
        //else, render 'delete' button'
        //else, render 'delete' button'
        else{
          return "<button class='btn btn-link btn-sm submit_button' onclick='update_form("+project.id+","+meta.row+")'>"+ getString(25) + "</button>"+
          "<br/>"+
          "<button class='btn btn-link btn-sm submit_button' onclick='delete_form("+project.id+","+meta.row+")'>" + getString(13) + "</button>";
        }
      }}
    ];


    var project_id = project.id;

    //add project ID to vars (that gets sent to AJAX call)
    vars.project_id = project_id;

    projectFormsParams = {
      vars: vars,
      action: "dt_project_forms",
      target: "forms_table_" + project.id,
      columns: projectFormsColumns,
      options: {
        dom: "tp",
        select: false,
        pageLength: 150,
        buttons: [
        {
            text: getString(14),
            action: function(e,dt,node,config){
              update_counts(dt);
            },
            className:"submit_button"
          },
          {
            text: getString(15),
            action: function(e,dt,node,config){
              downloaddata(project_id)
            },
            className:"submit_button"
          }
        ],
      },
      buttons_target: "buttons_for_forms_table" + project_id
      }

    //call datatables function
    table = newDatatable(projectFormsParams);

    projectFormsTable[project_id] = table;

  })


}

function delete_form(project_id,row_id){
  //get kobo_form_id for delete request

  var rowData =  projectFormsTable[project_id].row(row_id).data();

  console.log(rowData);

  var koboFormId = rowData.project_forms_info.form_kobo_id;
  var formId = rowData.project_forms_info.form_id;
  var dtId = rowData.DT_RowId;

  //// ########################## BELOW NOT TRANSLATED #############################
  if(confirm("Are you sure you want to delete the "+rowData.xls_forms.form_title+ " form from your Kobotoolbox account? This will permanently delete the form from Kobotoolbox. We will fetch any new data into this platform before deleltion to avoid data loss.")){
    var delRequest = jQuery.ajax({
      url: vars.node_url + "/customDeleteForm",
      method: "DELETE",
      dataType: "json",
      contentType: "application/json; charset=utf-8",
      data: JSON.stringify({
        kobo_id: koboFormId
      })
    })
    .done(function(response){

      //remove koboform id from database;
      projectFormId = rowData.project_forms_info.id;

      var dataUpdate = {}

      dataUpdate[dtId] = {
        DT_RowId: dtId,
        projects_xls_forms: {
          id: rowData.project_forms_info.id,
          project_id: rowData.project_forms_info.project_id,
          form_id: rowData.project_forms_info.form_id,
          form_kobo_id: null,
          deployed: rowData.project_forms_info.deployed,
          records: rowData.project_forms_info.records
        }
      };

      var delUpdate = jQuery.ajax({
        url: vars.ajax_url,
        method: "POST",
        dataType: "json",
        data:{
          action:"dt_project_forms_updater",
          secure:vars.nonce,
          dt_action:"edit",
          data: dataUpdate
        }
      })
      .done(function(response){
        projectFormsTable[project_id].ajax.reload();
        user_alert("kobotoolbox formID removed from platform database","info");
      })
      user_alert("Form with id " + formId + " has been deleted from Kobotoolbox. To continue using that form type, please deploy it again.","success");
    })
  }
  else{
    //cancel request and return states;
    user_alert('delete request cancelled','info');
    working();
  }



}

/*************** COPIED FROM NRC *********************/

////// Functions to update form record counts and pull new records from KOBO into the MySQL database:
///
function update_counts(dt){

  //present working() ui
  working("connecting to Kobotoolbox");

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
      working("getting records for " + form.xls_forms.form_title);

      request = requestFormCount(formId);
      requests.push(request);

      request.done(function(response){

        if(response.statusCode != 200) {
          user_alert("Unable to retrieve data from Kobotoolbox. Please check that Kobotoolbox is currently available. If this error persists, please contact support@stats4sd.org.","danger");

          throw("warning - unable to reach Kobotoolbox site to retrieve new data. Please check that the Kobotoolbox site is currently accessible through the browser");
        }
        formCountResponse(dt,response,form)
      });
    }


  });


  jQuery.when.apply(jQuery,requests).then(function(responses){
    working();
    user_alert(getString(24),"info","alert-space");
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
    working("no new records");
    return;
  }

  if(db_count > response.count){
    working("no new records");
    console.log("kobo_form with id" + response.kobo_id + "has fewer records than database")
    return;
  }

  if(db_count < response.count){

    var numNew = response.count - db_count;

    working(numNew + " new records found - adding them to the platform");


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
      numNew = response

      //add pulled data to collected_data table:
      response = response.body;


      savedata = {};
      response.forEach(function(row,index){

        console.log(index);
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

        working("success!");
        working();
        console.log("response from db: ",response);
        user_alert("New records successfully pulled for " + form.xls_forms.form_title,"success","alert-space");

        dt.ajax.reload();
      })

    })
    console.log();
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

    console.log("sample data = ",data)


    sampleValues[0] = {};
    sampleValues[0]["Dt_RowId"] = 0;


    //make date just the date;
    var sampleDate = data.date.substring(0,10);

    sampleValues[0]["samples"] = {};
    sampleValues[0]["samples"].id = data.sample_id
    sampleValues[0]["samples"].username = data.username

    // currently null
    sampleValues[0]["samples"].plot_id = data.plot_id


    sampleValues[0]["samples"].date = sampleDate || new Date()
    sampleValues[0]["samples"].depth = data.depth
    sampleValues[0]["samples"].texture = data.texture || ""
    sampleValues[0]["samples"].at_plot = data.at_plot || "0"

    //just pulls photo name/id. Need to pull actual photo next!
    sampleValues[0]["samples"].plot_photo = data.plot_photo || "";

    console.log("DATA GPS ", data._geolocation);
      //parse GPS:
      if(data._geolocation[0] != null){
        var gpsArray = data.location.split(" ");
        console.log("gps array",gpsArray);
        sampleValues[0]["samples"].latitude = gpsArray[0] || null
        sampleValues[0]["samples"].longitude = gpsArray[1] || null
        sampleValues[0]["samples"].altitude = gpsArray[2] || null
        sampleValues[0]["samples"].accuracy = gpsArray[3] || null
      }

      // pulled from pagee, not form data:
      sampleValues[0]["samples"].project_id = projectId;

      sampleValues[0]["samples"].comment = data.comms
      sampleValues[0]["samples"].farmer_quick = data.farmer || ""

      sampleValues[0]["samples"].community_quick = data.na_community || ""

      console.log("sample values to enter", sampleValues)
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
      user_alert("new samples from intake form added to database","info");
      console.log("response from inserting samples to db: ",response);
    })



  }

  if(formType == "ccrp_soil_p") {


    console.log("soils_p data = ",data)

    p = {};
    p[0] = {};

    var sample_id = "";
    if(data['bar_code']=='1'){
      sample_id = data['sample_id']
    }
    else {
      sample_id = data['no_bar_code']
    }

    p[0]["Dt_RowId"] = 0;
    p[0]["analysis_p"] = {
      sample_id: sample_id,
      analysis_date: data['analysis_date'],
      weight_soil: data['weight_soil'],
      vol_extract: data['vol_extract'],
      vol_topup: data['vol_topup'],
      color: data['color'],
      cloudy: data['cloudy'],
      raw_conc: data['raw_conc'],
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

    console.log("soils_ph data = ",data)
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

    console.log("soils_poxc data = ",data)
    poxc = {};
    poxc[0] = {};

    var sample_id = "";
    if(data['bar_code']=='1'){
      sample_id = data['sample_id']
    }
    else {
      sample_id = data['no_bar_code']
    }

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
      sample_id: sample_id,
      analysis_date: data['analysis_date'],
      weight_soil: data['weight_soil'],
      color: data['color'],
      color100: data['color100'],
      conc_digest: data['conc_digest'],
      cloudy: data['cloudy'],
      pct_reduction_color: data['pct_reduction_color'],
      raw_conc: data['raw_conc'],
      poxc_sample: data['poxc_sample'],
      poxc_soil: data['poxc_soil'],
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

  if(formType == "ccrp_soil_agg") {

    console.log("soils_agg data = ",data)
    agg = {};
    agg[0] = {};

    var sample_id = "";
    if(data['bar_code']=='1'){
      sample_id = data['sample_id']
    }
    else {
      sample_id = data['no_bar_code']
    }

    console.log("sample_id = ",sample_id)

    agg[0]["Dt_RowId"] = 0;
    agg[0]["analysis_agg"] = {
      sample_id: sample_id,
      analysis_date: data['analysis_date'],
      weight_soil: data['weight_soil'],
      weight_cloth: data['weight_cloth'],
      weight_stones2mm: data['weight_stones2mm'],
      weight_2mm_aggreg: data['weight_2mm_aggreg'],
      weight_cloth_250micron: data['weight_cloth_250micron'],
      weight_250micron_aggreg: data['weight_250micron_aggreg'],
      pct_stones: data['pct_stones'],
      twomm_aggreg_pct: data['twomm_aggreg_pct'],
      twofiftymicr_aggreg_pct: data['twofiftymicr_aggreg_pct'],
    }

    jQuery.ajax({
      url: vars.ajax_url,
      dataType: "json",
      method:"POST",
      data:{
        action:"dt_analysis_agg",
        secure:vars.nonce,
        dt_action:"create",
        data: agg
      }
    })
    .done(function(response){
      console.log("response from inserting agg to db: ",response);
    })

  }
}
