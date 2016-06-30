{config_load file="admin.ini" section="user-register"}
<div class="modal fade" id="modal-uadd" tabindex="-1" role="dialog" aria-labelledby="uadd-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{#AddUser#}</h4>
            </div>
            <div class="modal-body" id="mb-uadd">
                <form role="form" onsubmit="return false;">       
                    <div class="form-group">
                        <label>{#UserName#}</label>
                        <input type ="text" id="uadd-username" class="form-control" name="username">
                    </div>
                    <div class="form-group">
                        <label>{#Password#}</label>
                        <input type="password" id="uadd-passwd" name="passwd" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{#RPassword#}</label>
                        <input type="password" id="uadd-rpasswd" name="rpasswd" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{#Email#}</label>
                        <input type="text" id="uadd-email" class="form-control" name="email">
                    </div>
                    <div class="form-group">
                        <label>{#Name#}</label>
                        <input type="text" id="uadd-name" class="form-control" name="name">
                    </div>
                    <div class="form-group">
                        <label>{#Groups#}</label>
                        <select multiple class="form-control" id="uadd-selectg">
                            {foreach $groups as $group}
                                <option value="{$group.id}" data-desc="{$group.description}">{$group.name}</option>
                            {/foreach}
                        </select>
                        <p class="help-block">{#SelectHelp#}</p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{#Close#}</button>
                <button type="button" class="btn btn-primary" id="uadd-save">{#Save#}</button>
            </div>
        </div>
    </div>
</div>