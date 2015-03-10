$(document).ready(function() {
    if(globalobj.objclass == 'index' && globalobj.objmethod == 'index'){ //Content slider will be used only for non logged home page
        //big slider control start
        featuredcontentslider.init({
        id: "slider1",  //id of main slider DIV
        contentsource: ["inline", ""],  //Valid values: ["inline", ""] or ["ajax", "path_to_file"]
        toc: "markup",  //Valid values: "#increment", "markup", ["label1", "label2", etc]
        nextprev: ["Previous", "Next"],  //labels for "prev" and "next" links. Set to "" to hide.
        revealtype: "click",
        toolTip: true,//Behavior of pagination links to reveal the slides: "click" or "mouseover"
        enablefade: [true, 0.100],  //[true/false, fadedegree]
        autorotate: [true, 7000],  //[true/false, pausetime]
        onChange: function(previndex, curindex){  //event handler fired whenever script changes slide
            //previndex holds index of last slide viewed b4 current (1=1st slide, 2nd=2nd etc)
            //curindex holds index of currently shown slide (1=1st slide, 2nd=2nd etc)
            }
        });
        //big slider control end
    }
	
});

//tooltip start
$(function() {
  //$(".pagination-big img[title]").tooltip();
});
//tooltip end
