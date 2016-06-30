{config_load file="devices.ini" section="auth"}
<div class="modal fade" id="modal-devauth" tabindex="-1" role="dialog" aria-labelledby="devauth-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="devadd-t">{#AuthDetail#}</h4>
      </div>
      <div class="modal-body">
          <dl>
              <dt>{#GlobalAccess#}</dt>
              <dd id="access-global"></dd>
              <dt>{#AccessLocation#}</dt>
              <dd id="access-location"></dd>
              <dt>{#AccessObject#}</dt>
              <dd id="access-object"></dd>
          </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{#Close#}</button>
      </div>
    </div>
  </div>
</div>