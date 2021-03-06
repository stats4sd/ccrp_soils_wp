var districtsEditor;
var communitiesEditor;
jQuery(document).ready(function($){
	console.log("stats4sd-js Starting again");

  // *****************************************************
  // DISTRICTS
  // *****************************************************
  
  districtsFields = [
    {
      label: "Project:",
      info: "For test projects, select Research Methods or Soils",
      name: "districts.project",
      type: "select",
      placeholder: "select project",
    },
    {
      label: "District_ID:",
      name: "districts.id",
      type: "hidden" //this value is generated from concatenating the project-code and the entered string.
    },
    {
      label: "District Label:",
      name: "districts.district_label",
    },
    {
      label: "Country:",
      name: "districts.country_id",
      type: "select",
      placeholder: "Select a Country",
    }
  ]

  districtsColumns = [
    { data: "id", title: "More Info", render: function(data,type,row,meta){
       return "<span class='fa fa-plus-circle commButton' id='" + data + "'></span>";
     }, "className":"trPlus",visible:false },
    { data: "districts.id", title: "Code" },
    { data: "districts.district_label", title: "Name" },
    // { data: "countries.country_label", title: "Country Label" },
    { data: "wp_bp_groups.name", title: "Project" },
  ]

  districtsEditorParams = {
    vars: vars,
    action: "dt_districts",
    fields: districtsFields,
    table: "districtsTable",
    template: "districts-edit-template",
  }

  districtsEditor = newEditor(districtsEditorParams);

  // Handle SQL error generated when a duplicate community code is spotted:
  districtsEditor.on('postSubmit', function ( e, json, data, action) {
    console.log('submitted post');
    if(json.error) {
        console.log('sql error noticed');
          var codeField = this.field('districts.id');
          console.log(json)
          var sqlDupKey = json.error;
          console.log("sqlerror = ", sqlDupKey)

          //if error is for a duplicate Community Code, display error: 
          if(sqlDupKey.includes("SQLSTATE[23000]") && sqlDupKey.includes("id")) {
            codeField.error('This Code already exists in the database. Please choose a different code');
            //hide default display of error code. 
            json.error = 'Errors have been spotted in the data. Please see the highlighted fields above';
          }
        }
  })

  districtsTableParams = {
    vars: vars,
    action: "dt_districts",
    columns: districtsColumns,
    target: "districtsTable",
    editor: districtsEditor,
    buttons_target: "dt-buttons_for_districtsTable"
  }

  districtsTable = newDatatable(districtsTableParams);

  // districtsFilters = [
  //   {
  //     column_number: 4,
  //     filter_container_id: "district_projectFilter",
  //     filter_type:"select",
  //     filter_default_label:"Select Project",
  //     style_class:"form-control filter-control",
  //     filter_reset_button_text:"Reset"
  //   }
  // ]

  // newFilters(districtsFilters,districtsTable,vars);

  // *****************************************************
  // Villages / Communities
  // *****************************************************
  
  communitiesFields = [
    {
      label: "Project:",
      labelInfo: "For test projects, select Research Methods or Soils",
      name: "communities.project",
      type: "select",
      placeholder: "select project"
    },
    {
      label: "Village Code:",
      name: "communities.id",
      type: "hidden"
    },
    {
      label: "Code",
      name: "communities.code",
      ype: "hidden"
    },
    {
      label: "Village Name:",
      name: "communities.community_label"
    },
    {
      label: "District:",
      name: "communities.district_id",
      type: "select",
      placeholder: "Select a District",
    }
  ]

  communitiesColumns = [
    { data: "id", title: "More Info", render: function(data,type,row,meta){
      return "<span class='fa fa-plus-circle' id='commInfo_" + data + "'></span>";
      }, "className":"commButton",visible:true},
    { data: "communities.id", title: "Code" },
    { data: "communities.community_label", title: "Name" },
    { data: "communities.district_id", title: "District ID", visible: false},
    { data: "districts.district_label", title: "District Name" },
    { data: "wp_bp_groups.name", title: "Project" },
  ]

  communitiesEditorParams = {
    vars: vars,
    action: "dt_communities",
    fields: communitiesFields,
    table: "communitiesTable",
    template: "community-edit-template",
  }

  communitiesEditor = newEditor(communitiesEditorParams)

  //code for mysql primary-key clash
  communitiesEditor.on('postSubmit', function ( e, json, data, action) {
    console.log('submitted post');
    if(json.error) {
      console.log('sql error noticed');
        var codeField = this.field('communities.id');
        console.log(json)
        var sqlDupKey = json.error;
        console.log("sqlerror = ", sqlDupKey)

        //if error is for a duplicate Community Code, display error: 
        if(sqlDupKey.includes("SQLSTATE[23000]") && sqlDupKey.includes("id")) {
          codeField.error('This Code already exists in the database. Please choose a different code');
          //hide default display of error code. 
          json.error = 'Errors have been spotted in the data. Please see the highlighted fields above';
        }
      };

      location.reload();
  })

  communitiesTableParams = {
    vars: vars,
    action: "dt_communities",
    columns: communitiesColumns,
    target: "communitiesTable",
    editor: communitiesEditor,
    buttons_target: "dt-buttons_for_communitiesTable"
  }

  communitiesTable = newDatatable(communitiesTableParams)

  // communitiesTableFilters = [
  //   {
  //     column_number: 3,
  //     filter_container_id: "community_districtFilter",
  //     filter_type:"select",
  //     filter_default_label:"Select District",
  //     style_class:"form-control filter-control",
  //     filter_reset_button_text:"Reset"
  //   },
  //   {
  //     column_number: 4,
  //     filter_container_id: "community_projectFilter",
  //     filter_type:"select",
  //     filter_default_label:"Select Project",
  //     style_class:"form-control filter-control",
  //     filter_reset_button_text:"Reset"
  //   }
  // ];

  // //init new set of yadcf filters for the datatable
  // newFilters(communitiesTableFilters,communitiesTable,vars);

  // *****************************************************
  // Farms / Farmers
  // *****************************************************
  farmsFields = [
    {
      label: "Project",
      name: "farmers.project",
      type: "select",
      placeholder: "Select a project"
    },
    {
      label: "",
      name: "farmers.id",
      type: "hidden"
    },
    {
      label: "Farmer Name:",
      name: "farmers.farmer_name"
    },
    {
      label: "Community:",
      name: "farmers.community_id",
      type: "select",
      placeholder: "Select a Community"
    }
  ];

  farmsColumns = [
    { data: "id", title: "Show Barcode", render: function(data,type,row,meta){
      return "<span class='fa fa-plus-circle commButton' id='barcodeButton_" + data + "'></span>";
    }, "className":"trPlus"},
    { data: "farmers.community_id", title: "Village ID",visible: false},
    { data: "communities.community_label", title: "Village"},

    { data: "farmers.id", title: "Farmer Code" },
    { data: "farmers.farmer_name", title: "Farmer Name" },
    { data: "wp_bp_groups.name", title: "Project" }
  ],

  farmsEditorParams = {
    vars: vars,
    action: "dt_farms",
    fields: farmsFields,
    table: "farmsTable",
    template: "farm-edit-template",
  }

  farmsEditor = newEditor(farmsEditorParams);


  //farm editor validation code
  farmsEditor.on('initSubmit',function(e,action){
    if(farmsEditor.field('farmers.project').val() == '') {
      this.error('Please select a project');
      return false;
    }
    farmer_code = jQuery('#farmer_code_prefix').html()
    farmer_code += jQuery('#farmer_code_entered').val();
    farmsEditor.field('farmers.id').val(farmer_code);
    console.log('farmer_code calc',farmer_code);
    return true;
  });

  farmsTableParams = {
    vars: vars,
    action: "dt_farms",
    columns: farmsColumns,
    target: "farmsTable",
    editor: farmsEditor,
    buttons_target: "dt-buttons_for_farmsTable"
  }

  farmsTable = newDatatable(farmsTableParams);

  //farm editor validation code
  farmsEditor.on('initSubmit',function(e,action){
    if(farmsEditor.field('farmers.project').val() == '') {
      this.error('Please select a project');
      return false;
    }
    farmer_code = jQuery('#farmer_code_prefix').html()
    farmer_code += jQuery('#farmer_code_entered').val();
    farmsEditor.field('farmers.id').val(farmer_code);
    console.log('farmer_code calc',farmer_code);
    return true;
  });

  // *****************************************************
  // Setup of Communities Child-rows (to show aggregated Farm/farmer info) 
  // *****************************************************
  
  //setup child-row activation:
  jQuery('#communitiesTable tbody').on('click', 'td.commButton', function () {
    console.log("clicked");
    var tr = $(this).parents('tr');
    var row = communitiesTable.row( tr );
    console.log(row);
    if ( row.child.isShown() ) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    }
    else {
      //if there is a child row, open it.
      if(row.child() && row.child().length)
      {
        row.child.show();
      }
      else {
        //else, format it then show it.
        row.child(initialCommChildRow(row.data())).show();
        //get data for the row;
        var rdata = row.data();
        console.log("row data",rdata);
        community_label = rdata.communities.community_label;
        community_id = rdata.communities.id;

        var farmer_count = rdata.farmers.length;
        console.log("farmer_count = ",farmer_count);

        jQuery.get(vars.mustache_url + "/community_childrow.mst",function(template){
          var rendered = Mustache.render(template,{
            farmer_count: farmer_count,
            community_id: community_id
          });
          
          row.child(rendered).show();

          //setup barcode generator button:
          $("#Commgen_"+community_id).click(function(e){
            //generate codes
            jQuery.ajax({
              url: vars.ajax_url,
              "type":"POST",
              "dataType":"json",
              "data":{
                "action":"create_community_barcodes",
                "nonce":vars.pa_nonce,
                "farmers":rdata.farmers,
              },
              success: function(response){
                console.log("create_barcode response", response);

                setupCommCodeSheet(response.data,rdata);
              },
              error: function(jqXHR,textStatus,errorThrown){
                console.log("ajax error create barcodes",textStatus);
                console.log(errorThrown);
              }
            }); //end jquery ajax
          }); //end jQuery click
        }); //end get template
      } //end else
    } // end else
  }); //end on click commbuttons

  // *****************************************************
  // Setup of Farms Child-rows (to show QR codes and generate QR sheets)
  // *****************************************************
  jQuery('#farmsTable tbody').on('click', 'td.trPlus', function () {
    console.log("clicked");
    var tr = jQuery(this).parents('tr');
    var row = farmsTable.row( tr );
    console.log(row);
    if ( row.child.isShown() ) {
      // This row is already open - close it
      console.log("hiding row");
      row.child.hide();
      tr.removeClass('shown');
    }
    else {
      //if there is a child row, open it.

      if(row.child() && row.child().length) {
        console.log("showing row");
        row.child.show();
      }
      else {
        //else, format it then show it.
        console.log('making row');
        //show initial loading icon
        row.child(initialFarmChildRow(row.data())).show();

        //calculate the farmer code
        rdata = row.data();
        code = rdata.farmers.id;
        name = rdata.farmers.farmer_name;
        console.log('rdata',rdata);

        console.log('code',code);
        //go get the mustache template
        jQuery.get(vars.mustache_url + '/farm_childrow.mst',function(template){
          var rendered = Mustache.render(template,{code:code});
          console.log('rendered',rendered);
          row.child(rendered).show();

          //generate the QR code and put it into the template:
          new QRCode(document.getElementById("farmer_qr_code_"+code),{
            text: code,
            width: 100,
            height: 100,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
          });

          //setup barcode sample sheet button
          jQuery('#gen_'+code).click(function(e){

            //generate codes
            jQuery.ajax({
              url: vars.ajax_url,
              "type":"POST",
              "dataType":"json",
              "data":{
                "action":"create_barcode",
                "nonce":vars.pa_nonce,
                "root_id":code,
                "number":6
              },
              success: function(response){
                console.log("create_barcode response", response);
                codes = response.data;

                setup_codeSheet(codes,rdata.farmers);
              },
              error: function(jqXHR,textStatus,errorThrown){
                console.log("ajax error create barcodes: ",textStatus);
                console.log(errorThrown);
              }
            });
          });
        });
      } //end else
      tr.addClass('shown');
    }
  });


  // *****************************************************
  // Extra Editor Event Code
  // *****************************************************
  
  // districts
  districtsEditor.on('open displayOrder', function(e,mode,action){
    $("#DTE_Field_districts-project").change(function(){
      console.log('district project updated');
      console.log('project id = ',$(this).val());
      id = $(this).val();
      temp = [
        "NA","RMS","SOILS","FP"
      ]
      console.log('project code', temp[id]);
      $('#district_code_prefix').html(temp[id]);
    });
  });

  districtsEditor.on('initSubmit',function(e,action){
    if(districtsEditor.field('districts.project').val() == '') {
      this.error('Please select a project');
      return false;
    }
    district_code = jQuery('#district_code_prefix').html()
    district_code += jQuery('#district_code_entered').val();
    //
    //*** Districts have an id but no code field. Inconsistency needs to be fixed between tables!!!
    
    districtsEditor.field('districts.id').val(district_code);
    //districtsEditor.field('districts.code').val(district_code);
    console.log('district_code calc',district_code);
    return true;
  });

  //communities
  communitiesEditor.on('open displayOrder', function(e,mode,action){
    $("#DTE_Field_communities-district_id").change(function(){
      console.log('district updated');
      console.log('district id = ',$(this).val());
      id = $(this).val();
      $('#community_code_prefix').html(id);
    });
    $("#DTE_Field_communities-project").change(function(){
      var pro = $(this).val();
      temp = [
        "NA","RMS","SOILS","FP"
      ]

      pro = temp[pro]
      console.log(pro)
      $("#DTE_Field_communities-district_id option").each(function() {
        $(this).show();
        val = $(this).val();
        console.log(pro)
        console.log("val = ",val);
        console.log(val.search(pro))
        if(val.search(pro)!=0) {
          if(val!="") {
            $(this).hide();

          }
        }
      }); //end each
    });
  });

  communitiesEditor.on('initSubmit',function(e,action){
    if(communitiesEditor.field('communities.project').val() == '') {
      this.error('Please select a project');
      return false;
    }
    community_code = jQuery('#community_code_prefix').html();
    community_code += jQuery('#community_code_entered').val();
    communitiesEditor.field('communities.id').val(community_code);
  
    //THis is a hacky line - shouldn't have the same id and code. ID should either be gone or be an auto-increment number.
    communitiesEditor.field('communities.code').val(community_code);


    console.log('community_code calc',community_code);
    return true;
  });

  //farms
  farmsEditor.on('open displayOrder', function(e,mode,action){

    //need to set the farm_id entry value if editing

    jQuery("#DTE_Field_farmers-community_id").change(function(){
      console.log('community updated');
      console.log('community id = ',jQuery(this).val());
      id = jQuery(this).val();
      jQuery('#farmer_code_prefix').html(id);
    });

    jQuery("#DTE_Field_farmers-project").change(function(){
      var pro = jQuery(this).val();
      temp = ["NA","RMS","SOILS","FP"]

      pro = temp[pro]
      console.log(pro)
      jQuery("#DTE_Field_farmers-community_id option").each(function() {
        jQuery(this).show();
        val = jQuery(this).val();
        console.log(pro)
        console.log("val = ",val);
        console.log(val.search(pro))
        if(val.search(pro)!=0) {
          if(val!="") {
            jQuery(this).hide();
          }
        }
      }); //end each
    });
  });

  // *****************************************************
  // Link Tables by filter
  // *****************************************************
  
  //filter villages by selecting districts:
  communitiesFilterParams = {
    sourceTable: districtsTable,
    sourceCol: "districts.id",
    targetTable: communitiesTable,
    targetCol: 3
  }

  linkTableByFilter(communitiesFilterParams);

  //filter farms by selecting a village
  farmsFilterParams = {
    sourceTable: communitiesTable,
    sourceCol: "communities.id",
    targetTable: farmsTable,
    targetCol: 1
  }

  linkTableByFilter(farmsFilterParams);

}); //end document ready

function initialCommChildRow(data){
  return "<div id='child-row-" + data.communities.id + "'><span class='fa fa-spinner-circle'></span>Loading</div>";
}

function initialFarmChildRow(data){
  return "<div id='child-row-" + data.farmers.id + "'><span class='fa fa-spinner-circle'></span>Loading</div>";
}

function setupCommCodeSheet(data,rdata){
  //setup sheet for all farms in a community:
  
  //calculate page break points:
  
 
  jQuery.get(vars.mustache_url + "/multi-farm_samplecodes.mst", function(template){
    var rendered = Mustache.render(template,{
      community_code: rdata.communities.id,
      community_label: rdata.communities.community_label,
      farmers: data
      

    });
    console.log(rendered);

    jQuery('#comm_sample_sheet').html(rendered);
    jQuery('#comm_samplesheetmodal').modal('toggle');

    //page break points:
      
    jQuery(".farmer-block").each(function(index){
      if((index+1) % 2 == 0){
        jQuery(this).addClass('row')
      }
    })
    jQuery('.after-print').each(function(index){
      if((index+1) % 6 == 0) {
        jQuery(this).addClass('break-after');
        console.log('added pageBreak after index',index);
      }
      if((index+2) % 6 == 0) {
        jQuery(this).addClass('break-before');
        console.log("added break-before to index",index);
      }
      console.log('no pagebreak added to index',index);
    });

    //add qr codes: 
    //
    for(i = 0; i<data.length; i++) {

      elementId = "qr_" + data[i].farmer_id;
      farmerId = data[i].farmer_id;

      new QRCode(document.getElementById(elementId),{
        text: farmerId,
        width: 80,
        height: 80,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
      });

      sampleElementId = "qr_" + data[i].code;
      sample_code = data[i].code;

      new QRCode(document.getElementById(sampleElementId),{
        text: sample_code,
        width: 80,
        height: 80,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
      });
    }

    //setup print button;
    jQuery('#comm_printbutton_'+rdata.communities.id).click(function(){
      console.log('print button clicked');

      jQuery('#comm_print_modal_'+rdata.communities.id).printElement({
        pageTitle:"SampleSheet_"+rdata.communities.community_label+" - "+rdata.communities.id,
      });
      

    });

    // jQuery('#comm_printbutton_'+rdata.communities.id).printPreview({
    //   obj2print:'#comm_print_modal_'+rdata.communities.id,
    //   width: 810
    // });
  });
}
