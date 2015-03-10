$(document).ready(function() {
    if($(".photo-big").length > 0){
        $(".photo-big").lightbox({
            fitToScreen: true,
            scaleImages: true,
            xScale: 1,
            yScale: 1,
            displayDownloadLink: true
        });
    }

    if((globalobj.objclass == 'index' && globalobj.objmethod != 'index') || (globalobj.objclass != 'index' && globalobj.objmethod == 'index')){ //Content slider will be used only for non logged home page
        var slider1 =  $('#slider1').bxSlider({
            displaySlideQty: 8,
            moveSlideQty: 1,
            infiniteLoop: false,
            controls: false,
            auto: false
        });

        $('#go-prev').click(function(){
            slider1.goToPreviousSlide();
            return false;
        });

        $('#go-next').click(function(){
            slider1.goToNextSlide();
            return false;
        });

        var slider2 =  $('#slider2').bxSlider({
            displaySlideQty: 8,
            moveSlideQty: 1,
            infiniteLoop: false,
            controls: false,
            auto: false
        });

        $('#go-prev2').click(function(){
            slider2.goToPreviousSlide();
            return false;
        });

        $('#go-next2').click(function(){
            slider2.goToNextSlide();
            return false;
        });

        var slider3 =  $('#slider3').bxSlider({
            displaySlideQty: 8,
            moveSlideQty: 1,
            infiniteLoop: false,
            controls: false,
            auto: false
        });

        $('#go-prev3').click(function(){
            slider3.goToPreviousSlide();
            return false;
        });

        $('#go-next3').click(function(){
            slider3.goToNextSlide();
            return false;
        });
	
    }
});
