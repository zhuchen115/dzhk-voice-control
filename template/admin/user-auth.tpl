{config_load file="admin.ini" section="user-auth"}
<div class="modal fade" id="modal-uauth" tabindex="-1" role="dialog" aria-labelledby="uauth-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{#ChangeAuth#}</h4>
            </div>
            <div class="modal-body" id="mb-uauth">
                <form role="form" onsubmit="return false;">
                    <div class="form-group">
                        <label>UID</label>
                        <p class ="form-control-static" id="uauth-uid"></p>
                    </div>
                    <div class="form-group">
                        <label>{#UserName#}</label>
                        <p class ="form-control-static" id="uauth-username"></p>
                    </div>
                    <div class="form-group">
                        <fieldset disabled="disabled">
                            <label>{#AuthGlobal#}</label>
                        <label><input type="radio" class="radio-inline" name="access-global" id="uauth-global-y" value="1">{#Yes#}</label>
                        <label><input type="radio" class="radio-inline" name="access-global" id="uauth-global-n" value="0">{#No#}</label>
                        <p class="help-block">{#GlobalNoChange#}</p>
                        </fieldset>
                    </div>
                    <div class="form-group">
                        <label>{#ChangeAuthU#}</label>
                        <select class="form-control" id="uauth-selectl" multiple>
                            {foreach $locations as $location}
                                <option value="{$location.name}" data-type="{$location.type}">{$location.name}</option>
                            {/foreach}
                        </select>
                        <p class="help-block">{#DisabledItem#}</p>
                        <p class="help-block">{#SelectHelp#}</p>
                    </div>
                    <div class="form-group">
                        <label>{#ChangeAuthO#}</label>
                        <select class="form-control" id="uauth-selecto" multiple>
                            {foreach $objects as $obj}
                                <option value="{$obj.hsref}" data-location1="{$obj.location}" data-location2="{$obj.location2}">{$obj.location2}/{$obj.location}/{$obj.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{#Close#}</button>
                <button type="button" class="btn btn-primary" id="uauth-save">{#Save#}</button>
            </div>
        </div>
    </div>
</div>