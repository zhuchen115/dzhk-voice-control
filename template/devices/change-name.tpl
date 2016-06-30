{config_load file="devices.ini" section="cname"}
<div class="modal fade" id="modal-cname" tabindex="-1" role="dialog" aria-labelledby="cname-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="devchn-t">{#ChangeName#}</h4>
      </div>
      <div class="modal-body" id="mb-chname">
          <form role="form">
              <div class="form-group">
                  <label>{#DeviceID#}</label>
                  <p class ="form-control-static" id="cname-id"></p>
              </div>
              <div class="form-group">
                  <label>{#DeviceHID#}</label>
                  <p class ="form-control-static" id="cname-hid"></p>
              </div>
              <div class="form-group">
                  <label>{#DeviceName#}</label>
                  <input type="text" id="cname-name" name="devname" class="form-control">
                  <p class="help-block">{#DevNameDesc#}</p>
              </div>
          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{#Close#}</button>
        <button type="button" class="btn btn-primary" id="cname-save">{#Save#}</button>
      </div>
    </div>
  </div>
</div>