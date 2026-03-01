if (fpcm === undefined) {
    var fpcm = {};
}

fpcm.slider = {

    init: function () {

        if (fpcm.dataview !== undefined) {
            fpcm.dataview.render('nkorgslider');
        }

        fpcm.dom.bindClick("button[data-fn='delete']", function (_e) {

            fpcm.ajax.post('slider/actions', {
                data: {
                    id: _e.currentTarget.dataset.id,
                    fn: 'delete'
                },
                execDone: function () {
                    fpcm.ui.relocate(window.location.href);
                }
            });
        });

    }

};