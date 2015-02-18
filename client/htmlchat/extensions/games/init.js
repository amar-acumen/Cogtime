if (typeof(topcmm_games) == 'undefined' || topcmm_games.length == 0)
{
    var topcmm_games = "http://game.123flashchat.com/game.html";
}

topcmm.extensions.games = {
    tip: "Games",
    position: "top",
    width: "484",
    height: "364",
    display: {
        type: "icon",
        value: "img/common/topcmm-123flashchat-games-icon.png"
    },
    content: "<div style=\"float:left;\"><iframe scrolling=\"no\" frameborder=\"no\" style=\"border: 0px none; overflow: hidden; width: 484px; height: 364px;\" src=\"" + topcmm_games + "\"></iframe></div>"
}
