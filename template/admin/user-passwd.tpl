{config_load file="admin.ini" section="user-chpasswd"}
<div class="modal fade" id="modal-upasswd" tabindex="-1" role="dialog" aria-labelledby="upasswd-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{#ChangePasswd#}</h4>
      </div>
      <div class="modal-body" id="mb-chpasswd">
          <form role="form">
              <div class="form-group">
                  <label>UID</label>
                  <p class ="form-control-static" id="chpasswd-uid"></p>
              </div>
              <div class="form-group">
                  <label>{#UserName#}</label>
                  <p class ="form-control-static" id="chpasswd-username"></p>
              </div>
              <div class="form-group">
                  <label>{#NewPassword#}</label>
                  <input type="password" id="chpasswd-passwd" name="passwd" class="form-control">
                  <p class="help-block">{#PasswdDesc#}</p>
              </div>
              <div class="form-group">
                  <label>{#RPassword#}</label>
                  <input type="password" id="chpasswd-rpasswd" name="rpasswd" class="form-control">
              </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{#Close#}</button>
        <button type="button" class="btn btn-primary" id="chpasswd-save">{#Save#}</button>
      </div>
    </div>
  </div>
</div>