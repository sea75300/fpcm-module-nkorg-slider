if (fpcm === undefined) {
    var fpcm = {};
}

fpcm.slider_editor = {

    init: function () {
       
        fpcm.ui.autocomplete('#objurl', {
            source: fpcm.vars.ajaxActionPath + 'slider/autocomplete'
        });
    }

};