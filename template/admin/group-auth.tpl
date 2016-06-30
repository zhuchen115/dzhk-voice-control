{config_load file="admin.ini" section="group-auth"}
<div class="modal fade" id="modal-gauth" tabindex="-1" role="dialog" aria-labelledby="gauth-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{#GroupAuth#}</h4>
            </div>
            <div class="modal-body" id="mb-gauth">
                <form role="form" onsubmit="return false;">       
                    <div class="form-group">
                        <label>{#GroupID#}</label>
                        <p id="gauth-gid" class="form-control-static"></p>
                    </div>
                    <div class="form-group">
                        <label>{#GroupName#}</label>
                        <p id="gauth-gname" class="form-control-static"></p>
                    </div>
                    <div class="form-group">
                        <label>{#AuthGlobal#}</label>
                        <label><input type="radio" class="radio-inline" name="gauth-global" id="gauth-global-y" value="1">{#Yes#}</label>
                        <label><input type="radio" class="radio-inline" name="gauth-global" id="gauth-global-n" value="0">{#No#}</label>
                        <p class="help-block">{#GlobalNoChange#}</p>
                    </div>
                    <div class="form-group">
                        <label>{#ChangeAuthU#}</label>
                        <select class="form-control" id="gauth-selectl" multiple>
                            {foreach $locations as $location}
                                <option value="{$location.name}" data-type="{$location.type}">{$location.name}</option>
                            {/foreach}
                        </select>
                        <p class="help-block">{#SelectHelp#}</p>
                    </div>
                    <div class="form-group">
                        <label>{#ChangeAuthO#}</label>
                        <select class="form-control" id="gauth-selecto" multiple>
                            {foreach $objects as $obj}
                                <option value="{$obj.hsref}" data-location1="{$obj.location}" data-location2="{$obj.location2}">{$obj.location2}/{$obj.location}/{$obj.name}</option>
                            {/foreach}
                        </select>
                        <p class="help-block">{#SelectHelp#}</p>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{#Close#}</button>
                <button type="button" class="btn btn-primary" id="gauth-save">{#Save#}</button>
            </div>
        </div>
    </div>
</div>