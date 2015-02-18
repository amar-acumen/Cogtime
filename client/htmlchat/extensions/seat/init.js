function getSeatsHtml()
{
    return "<div style=\"bottom: 165px;height: 95px;left: 0;position: absolute;right: 0;\"><iframe src='extensions/seat/seats.html' style='left:0px; right:0px; top:0px; bottom:0px;position: relative;width: 100%; height: 100%; overflow: hidden; z-index:10000;border-style: none;background: transparent;' allowTransparency='true'></iframe></div>";
}

topcmm.extensions.seat = {
    position: "video_bottom",
    display: {
        type: "html",
        value: getSeatsHtml()
    }
}
