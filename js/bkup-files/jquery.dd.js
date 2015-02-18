// MSDropDown - jquery.dd.js
// author: Marghoob Suleman
// Date: 12th Aug, 2009
// Version: 2.1 {date: 3rd Sep 2009}
// Revision: 25
// web: www.giftlelo.com | www.marghoobsuleman.com
;
(function ($) {
    var D = "";
    $.fn.dd = function (v) {
        $this = this;
        v = $.extend({
            height: 120,
            visibleRows: 7,
            rowHeight: 23,
            showIcon: true,
            zIndex: 9999,
            style: ''
        }, v);
        var w = "";
        var x = {};
        x.insideWindow = true;
        x.keyboardAction = false;
        x.currentKey = null;
        var y = false;
        config = {
            postElementHolder: '_msddHolder',
            postID: '_msdd',
            postTitleID: '_title',
            postTitleTextID: '_titletext',
            postChildID: '_child',
            postAID: '_msa',
            postOPTAID: '_msopta',
            postInputID: '_msinput',
            postArrowID: '_arrow',
            postInputhidden: '_inp'
        };
        styles = {
            dd: 'dd',
            ddTitle: 'ddTitle',
            arrow: 'arrow',
            ddChild: 'ddChild',
            disbaled: .30
        };
        attributes = {
            actions: "onfocus,onblur,onchange,onclick,ondblclick,onmousedown,onmouseup,onmouseover,onmousemove,onmouseout,onkeypress,onkeydown,onkeyup",
            prop: "size,multiple,disabled,tabindex"
        };
        var z = $(this).attr("id");
        var A = $(this).attr("style");
        v.style += (A == undefined) ? "" : A;
        var B = $(this).children();
        y = ($(this).attr("size") > 0 || $(this).attr("multiple") == true) ? true : false;
        if (y) {
            v.visibleRows = $(this).attr("size")
        };
        var C = {};
        createDropDown();

        function getPostID(a) {
            return z + config[a]
        };

        function getOptionsProperties(a) {
            var b = a;
            var c = $(b).attr("style");
            return c
        };

        function matchIndex(a) {
            var b = $("#" + z + " option:selected");
            if (b.length > 1) {
                for (var i = 0; i < b.length; i++) {
                    if (a == b[i].index) {
                        return true
                    }
                }
            } else if (b.length == 1) {
                if (b[0].index == a) {
                    return true
                }
            };
            return false
        }
        function createATags() {
            var r = B;
            var s = "";
            var t = getPostID("postAID");
            var u = getPostID("postOPTAID");
            r.each(function (i) {
                var j = r[i];
                if (j.nodeName == "OPTGROUP") {
                    s += "<div class='opta'>";
                    s += "<span style='font-weight:bold;font-style:italic; clear:both;'>" + $(j).attr("label") + "</span>";
                    var k = $(j).children();
                    k.each(function (a) {
                        var b = k[a];
                        var c = u + "_" + (i) + "_" + (a);
                        var d = $(b).attr("title");
                        d = (d.length == 0) ? "" : '<img src="' + d + '" align="left" /> ';
                        var e = $(b).text();
                        var f = $(b).val();
                        var g = ($(b).attr("disabled") == true) ? "disabled" : "enabled";
                        C[c] = {
                            html: d + e,
                            value: f,
                            text: e,
                            index: b.index,
                            id: c
                        };
                        var h = getOptionsProperties(b);
                        if (matchIndex(b.index) == true) {
                            s += '<a href="javascript:void(0);" class="selected ' + g + '"'
                        } else {
                            s += '<a  href="javascript:void(0);" class="' + g + '"'
                        };
                        if (h != false) s += ' style="' + h + '"';
                        s += ' id="' + c + '">';
                        s += d + e + '</a>'
                    });
                    s += "</div>"
                } else {
                    var l = t + "_" + (i);
                    var m = $(j).attr("title");
                    /*m = (m.length == 0) ? "" : '<img src="' + m + '" align="left" /> ';*/
                    var n = $(j).text();
                    var o = $(j).val();
                    var p = ($(j).attr("disabled") == true) ? "disabled" : "enabled";
                    C[l] = {
                        html: m + n,
                        value: o,
                        text: n,
                        index: j.index,
                        id: l
                    };
                    var q = getOptionsProperties(j);
                    if (matchIndex(j.index) == true) {
                        s += '<a href="javascript:void(0);" class="selected ' + p + '"'
                    } else {
                        s += '<a  href="javascript:void(0);" class="' + p + '"'
                    };
                    if (q != false) s += ' style="' + q + '"';
                    s += ' id="' + l + '">';
                    s += m + n + '</a>'
                }
            });
            return s
        };

        function createChildDiv() {
            var a = getPostID("postID");
            var b = getPostID("postChildID");
            var c = v.style;
            sDiv = "";
            sDiv += '<div id="' + b + '" class="' + styles.ddChild + '"';
            if (!y) {
                sDiv += (c != "") ? ' style="' + c + '"' : ''
            } else {
                sDiv += (c != "") ? ' style="border-top:1px solid #c3c3c3;display:block;position:relative;' + c + '"' : ''
            }
            sDiv += '>';
            return sDiv
        };

        function createTitleDiv() {
            var a = getPostID("postTitleID");
            var b = getPostID("postArrowID");
            var c = getPostID("postTitleTextID");
            var d = getPostID("postInputhidden");
            var e = $("#" + z + " option:selected").text();
            var f = $("#" + z + " option:selected").attr("title");
            /*f = (f.length == 0 || f == undefined || v.showIcon == false) ? "" : '<img src="' + f + '" align="left" /> ';*/
            var g = '<div id="' + a + '" class="' + styles.ddTitle + '"';
            g += '>';
            g += '<span id="' + b + '" class="' + styles.arrow + '"></span><span class="textTitle" id="' + c + '">' + f + e + '</span></div>';
            return g
        };

        function createDropDown() {
            var d = false;
            var e = getPostID("postID");
            var f = getPostID("postTitleID");
            var g = getPostID("postTitleTextID");
            var h = getPostID("postChildID");
            var i = getPostID("postArrowID");
            var j = $("#" + z).width();
            var k = v.style;
            if ($("#" + e).length > 0) {
                $("#" + e).remove();
                d = true
            }
            var l = '<div id="' + e + '" class="' + styles.dd + '"';
            l += (k != "") ? ' style="' + k + '"' : '';
            l += '>';
            if (!y) l += createTitleDiv();
            l += createChildDiv();
            l += createATags();
            l += "</div>";
            l += "</div>";
            if (d == true) {
                var m = getPostID("postElementHolder");
                $("#" + m).after(l)
            } else {
                $("#" + z).after(l)
            }
            $("#" + e).css("width", j + "px");
            $("#" + h).css("width", (j - 2) + "px");
            if (B.length > v.visibleRows) {
                var n = parseInt($("#" + h + " a:first").css("padding-bottom")) + parseInt($("#" + h + " a:first").css("padding-top"));
                var o = ((v.rowHeight) * v.visibleRows) - n;
                $("#" + h).css("height", o + "px")
            }
            if (d == false) {
                setOutOfVision();
                addNewEvents(z)
            }
            if ($("#" + z).attr("disabled") == true) {
                $("#" + e).css("opacity", styles.disbaled)
            } else {
                applyEvents();
                if (!y) {
                    $("#" + f).bind("mouseover", function (a) {
                        hightlightArrow(1)
                    });
                    $("#" + f).bind("mouseout", function (a) {
                        hightlightArrow(0)
                    })
                };
                $("#" + h + " a.enabled").bind("click", function (a) {
                    a.preventDefault();
                    manageSelection(this);
                    if (!y) {
                        $("#" + h).unbind("mouseover");
                        setInsideWindow(false);
                        var b = (v.showIcon == false) ? $(this).text() : $(this).html();
                        setTitleText(b);
                        closeMe()
                    };
                    setValue()
                });
                $("#" + h + " a.disabled").css("opacity", styles.disbaled);
                if (y) {
                    $("#" + h).bind("mouseover", function (c) {
                        if (!x.keyboardAction) {
                            x.keyboardAction = true;
                            $(document).bind("keydown", function (a) {
                                var b = a.keyCode;
                                x.currentKey = b;
                                if (b == 39 || b == 40) {
                                    a.preventDefault();
                                    a.stopPropagation();
                                    next();
                                    setValue()
                                };
                                if (b == 37 || b == 38) {
                                    a.preventDefault();
                                    a.stopPropagation();
                                    previous();
                                    setValue()
                                }
                            })
                        }
                    })
                };
                $("#" + h).bind("mouseout", function (a) {
                    setInsideWindow(false);
                    $(document).unbind("keydown");
                    x.keyboardAction = false;
                    x.currentKey = null
                });
                if (!y) {
                    $("#" + f).bind("click", function (b) {
                        setInsideWindow(false);
                        if ($("#" + h + ":visible").length == 1) {
                            $("#" + h).unbind("mouseover")
                        } else {
                            $("#" + h).bind("mouseover", function (a) {
                                setInsideWindow(true)
                            });
                            openMe()
                        }
                    })
                };
                $("#" + f).bind("mouseout", function (a) {
                    setInsideWindow(false)
                })
            }
        };

        function getByIndex(a) {
            for (var i in C) {
                if (C[i].index == a) {
                    return C[i]
                }
            }
        }
        function manageSelection(a) {
            var b = getPostID("postChildID");
            if (!y) {
                $("#" + b + " a.selected").removeClass("selected")
            }
            var c = $("#" + b + " a.selected").attr("id");
            if (c != undefined) {
                var d = (x.oldIndex == undefined || x.oldIndex == null) ? C[c].index : x.oldIndex
            };
            if (a && !y) {
                $(a).addClass("selected")
            };
            if (y) {
                var e = x.currentKey;
                if ($("#" + z).attr("multiple") == true) {
                    if (e == 17) {
                        x.oldIndex = C[$(a).attr("id")].index;
                        $(a).toggleClass("selected")
                    } else if (e == 16) {
                        $("#" + b + " a.selected").removeClass("selected");
                        $(a).addClass("selected");
                        var f = $(a).attr("id");
                        var g = C[f].index;
                        for (var i = Math.min(d, g); i <= Math.max(d, g); i++) {
                            $("#" + getByIndex(i).id).addClass("selected")
                        }
                    } else {
                        $("#" + b + " a.selected").removeClass("selected");
                        $(a).addClass("selected");
                        x.oldIndex = C[$(a).attr("id")].index
                    }
                } else {
                    $("#" + b + " a.selected").removeClass("selected");
                    $(a).addClass("selected");
                    x.oldIndex = C[$(a).attr("id")].index
                }
            }
        };

        function addNewEvents(a) {
            document.getElementById(a).refresh = function (e) {
                $("#" + this.id).dd(v)
            }
        };

        function setInsideWindow(a) {
            x.insideWindow = a
        };

        function getInsideWindow() {
            return x.insideWindow
        };

        function applyEvents() {
            var b = getPostID("postID");
            var c = attributes.actions.split(",");
            for (var d = 0; d < c.length; d++) {
                var e = c[d];
                var f = $("#" + z).attr(e);
                if (f != undefined) {
                    switch (e) {
                    case "onfocus":
                        $("#" + b).bind("mouseenter", function (a) {
                            document.getElementById(z).focus()
                        });
                        break;
                    case "onclick":
                        $("#" + b).bind("click", function (a) {
                            document.getElementById(z).onclick()
                        });
                        break;
                    case "ondblclick":
                        $("#" + b).bind("dblclick", function (a) {
                            document.getElementById(z).ondblclick()
                        });
                        break;
                    case "onmousedown":
                        $("#" + b).bind("mousedown", function (a) {
                            document.getElementById(z).onmousedown()
                        });
                        break;
                    case "onmouseup":
                        $("#" + b).bind("mouseup", function (a) {
                            document.getElementById(z).onmouseup()
                        });
                        break;
                    case "onmouseover":
                        $("#" + b).bind("mouseover", function (a) {
                            document.getElementById(z).onmouseover()
                        });
                        break;
                    case "onmousemove":
                        $("#" + b).bind("mousemove", function (a) {
                            document.getElementById(z).onmousemove()
                        });
                        break;
                    case "onmouseout":
                        $("#" + b).bind("mouseout", function (a) {
                            document.getElementById(z).onmouseout()
                        });
                        break
                    }
                }
            }
        };

        function setOutOfVision() {
            var a = getPostID("postElementHolder");
            $("#" + z).after("<div style='height:0px;overflow:hidden;position:absolute;' id='" + a + "'></div>");
            $("#" + z).appendTo($("#" + a))
        };

        function setTitleText(a) {
            var b = getPostID("postTitleTextID");
            $("#" + b).html(a)
        };

        function next() {
            var a = getPostID("postTitleTextID");
            var b = getPostID("postChildID");
            var c = $("#" + b + " a.enabled");
            for (var d = 0; d < c.length; d++) {
                var e = c[d];
                var f = $(e).attr("id");
                if ($(e).hasClass("selected") && d < c.length - 1) {
                    $("#" + b + " a.selected").removeClass("selected");
                    $(c[d + 1]).addClass("selected");
                    var g = $("#" + b + " a.selected").attr("id");
                    if (!y) {
                        var h = (v.showIcon == false) ? C[g].text : C[g].html;
                        setTitleText(h)
                    }
                    if (parseInt(($("#" + g).position().top + $("#" + g).height())) >= parseInt($("#" + b).height())) {
                        $("#" + b).scrollTop(($("#" + b).scrollTop()) + $("#" + g).height() + $("#" + g).height())
                    };
                    break
                }
            }
        };

        function previous() {
            var a = getPostID("postTitleTextID");
            var b = getPostID("postChildID");
            var c = $("#" + b + " a.enabled");
            for (var d = 0; d < c.length; d++) {
                var e = c[d];
                var f = $(e).attr("id");
                if ($(e).hasClass("selected") && d != 0) {
                    $("#" + b + " a.selected").removeClass("selected");
                    $(c[d - 1]).addClass("selected");
                    var g = $("#" + b + " a.selected").attr("id");
                    if (!y) {
                        var h = (v.showIcon == false) ? C[g].text : C[g].html;
                        setTitleText(h)
                    }
                    if (parseInt(($("#" + g).position().top + $("#" + g).height())) <= 0) {
                        $("#" + b).scrollTop(($("#" + b).scrollTop() - $("#" + b).height()) - $("#" + g).height())
                    };
                    break
                }
            }
        };

        function setValue() {
            var a = getPostID("postChildID");
            var b = $("#" + a + " a.selected");
            if (b.length == 1) {
                var c = $("#" + a + " a.selected").text();
                var d = $("#" + a + " a.selected").attr("id");
                if (d != undefined) {
                    var e = C[d].value;
                    document.getElementById(z).selectedIndex = C[d].index
                }
            } else if (b.length > 1) {
                var f = $("#" + z + " > option:selected").removeAttr("selected");
                for (var i = 0; i < b.length; i++) {
                    var d = $(b[i]).attr("id");
                    var g = C[d].index;
                    document.getElementById(z).options[g].selected = "selected"
                }
            }
        };

        function openMe() {
            var c = getPostID("postChildID");
            if (D != "" && c != D) {
                $("#" + D).slideUp("fast");
                $("#" + D).css({
                    zIndex: '0'
                })
            };
            if ($("#" + c).css("display") == "none") {
                w = C[$("#" + c + " a.selected").attr("id")].text;
                $(document).bind("keydown", function (a) {
                    var b = a.keyCode;
                    if (b == 39 || b == 40) {
                        a.preventDefault();
                        a.stopPropagation();
                        next()
                    };
                    if (b == 37 || b == 38) {
                        a.preventDefault();
                        a.stopPropagation();
                        previous()
                    };
                    if (b == 27 || b == 13) {
                        closeMe();
                        setValue()
                    };
                    if ($("#" + z).attr("onkeydown") != undefined) {
                        document.getElementById(z).onkeydown()
                    }
                });
                $(document).bind("keyup", function (a) {
                    if ($("#" + z).attr("onkeyup") != undefined) {
                        document.getElementById(z).onkeyup()
                    }
                });
                $(document).bind("mouseup", function (a) {
                    if (getInsideWindow() == false) {
                        closeMe()
                    }
                });
                $("#" + c).css({
                    zIndex: v.zIndex
                });
                $("#" + c).slideDown("fast");
                if (c != D) {
                    D = c
                }
            }
        };

        function closeMe() {
            var b = getPostID("postChildID");
            $(document).unbind("keydown");
            $(document).unbind("keyup");
            $(document).unbind("mouseup");
            $("#" + b).slideUp("fast", function (a) {
                checkMethodAndApply();
                $("#" + b).css({
                    zIndex: '0'
                })
            })
        };

        function checkMethodAndApply() {
            var b = getPostID("postChildID");
            if ($("#" + z).attr("onchange") != undefined) {
                var c = C[$("#" + b + " a.selected").attr("id")].text;
                if (w != c) {
                    document.getElementById(z).onchange()
                }
            }
            if ($("#" + z).attr("onmouseup") != undefined) {
                document.getElementById(z).onmouseup()
            }
            if ($("#" + z).attr("onblur") != undefined) {
                $(document).bind("mouseup", function (a) {
                    $("#" + z).focus();
                    $("#" + z)[0].blur();
                    setValue();
                    $(document).unbind("mouseup")
                })
            }
        };

        function hightlightArrow(a) {
            var b = getPostID("postArrowID");
            if (a == 1) $("#" + b).css({
                backgroundPosition: '0 100%'
            });
            else $("#" + b).css({
                backgroundPosition: '0 0'
            })
        }
    };
    $.fn.msDropDown = function (a) {
        var b = $(this);
        for (var c = 0; c < b.length; c++) {
            var d = $(b[c]).attr("id");
            if (a == undefined) {
                $("#" + d).dd()
            } else {
                $("#" + d).dd(a)
            }
        }
    }
})(jQuery);