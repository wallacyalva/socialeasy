<div class="modal fade" id="PopupAddTags" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-grey">
                <h4 class="modal-title" id="defaultModalLabel"><?=l('Add Keywords')?></h4>
            </div>
            <div class="modal-body pt0">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active in" id="profile">
                        <div class="form-group">
                            <div class="form-line">
                                <textarea rows="4" class="form-control no-resize popup_list_tags" placeholder="<?=l('keywordA,keywordB,keywordC...')?>"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background: #F5F5F5;">
                <button type="button" class="btn bg-light-green waves-effect btnAddBlacklistKeywords"><?=l('Add Keywords')?></button>
                <button type="button" class="btn bg-grey waves-effect" data-dismiss="modal"><?=l('Close')?></button>
            </div>
        </div>
    </div>
</div>