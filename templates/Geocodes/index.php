<?= $this->Form->create($geocode, ['url' => ['action' => 'add']]); ?>
<div class="mb-3">
  <label for="ip_address" class="form-label">IP Address</label>
  <?= $this->Form->input('ip_address', ['class' => 'form-control form-control-lg']); ?>
  <div id="textHelp" class="form-text">We use this to figure out where you are! We'll know even if <em>you</em> don't!</div>
</div>
<?= $this->Form->button(__('Save My Hair!'), ['class' => 'btn btn-primary w-100']); ?>

<?= $this->Form->end(); ?>
