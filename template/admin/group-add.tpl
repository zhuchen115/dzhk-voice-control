{config_load file="admin.ini" section="group-add"}
<div class="modal fade" id="modal-gadd" tabindex="-1" role="dialog" aria-labelledby="gadd-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{#AddGroup#}</h4>
            </div>
            <div class="modal-body" id="mb-gadd">
                <form role="form" onsubmit="return false;">       
                    <div class="form-group">
                        <label>{#GroupID#}</label>
                        <input type ="text" id="gadd-gid" class="form-control" name="gid" value="0">
                        <p class="help-block">{#GIDHelp#}</p>
                    </div>
                    <div class="form-group">
                        <label>{#GroupName#}</label>
                        <input type="text" id="gadd-name" name="gname" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{#GroupDesc#}</label>
                        <input type="text" id="gadd-desc" class="form-control" name="gdesc">
                    </div>
                    <div class="form-group">
                        <label>{#AuthGlobal#}</label>
                        <label><input type="radio" class="radio-inline" name="gadd-global" id="gdd-global-y" value="1">{#Yes#}</label>
                        <label><input type="radio" class="radio-inline" name="gadd-global" id="gdd-global-n" value="0">{#No#}</label>
                        <p class="help-block">{#GlobalNoChange#}</p>
                    </div>
                    <div class="form-group">
                        <label>{#ChangeAuthU#}</label>
                        <select class="form-control" id="gadd-selectl" multiple>
                            {foreach $locations as $location}
                                <option value="{$location.name}" data-type="{$location.type}">{$location.name}</option>
                            {/foreach}
                        </select>
                        <p class="help-block">{#SelectHelp#}</p>
                    </div>
                    <div class="form-group">
                        <label>{#ChangeAuthO#}</label>
                        <select class="form-control" id="gadd-selecto" multiple>
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
                <button type="button" class="btn btn-primary" id="gadd-save">{#Save#}</button>
            </div>
        </div>
    </div>
</div>