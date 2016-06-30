{config_load file="admin.ini" section="user-groups"}
<div class="modal fade" id="modal-ugroups" tabindex="-1" role="dialog" aria-labelledby="ugroups-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{#EditGroups#}</h4>
            </div>
            <div class="modal-body" id="mb-chgroups">
                <form role="form">
                    <div class="form-group">
                        <label>UID</label>
                        <p class ="form-control-static" id="chgroups-uid"></p>
                    </div>
                    <div class="form-group">
                        <label>{#UserName#}</label>
                        <p class ="form-control-static" id="chgroups-username"></p>
                    </div>
                    <div class="form-group">
                        <label>{#SelectGroups#}</label>
                        <select multiple class="form-control" id="chgroups-select">
                            {foreach $groups as $group}
                                <option value="{$group.id}" data-desc="{$group.description}">{$group.name}</option>
                            {/foreach}
                        </select>
                        <p class="help-block">{#SelectHelp#}</p>
                        <p class="help-block" id="chgroups-gdesc"></p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{#Close#}</button>
                <button type="button" class="btn btn-primary" id="chgroups-save">{#Save#}</button>
            </div>
        </div>
    </div>
</div>