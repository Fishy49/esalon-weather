<?= $this->Form->create($geocode, ['url' => ['action' => 'add']]); ?>
<div class="mb-3">
  <label for="ip_address" class="form-label">IP Address</label>
  <?= $this->Form->input('ip_address', ['class' => 'form-control form-control-lg']); ?>
  <div id="textHelp" class="form-text">We use this to figure out where you are! We'll know even if <em>you</em> don't!</div>
</div>
<?= $this->Form->button(__('Save My Hair!'), ['class' => 'btn btn-primary w-100']); ?>

<?= $this->Form->end(); ?>

<hr>

<h2>Hair Forecast for: <em><?= $weather->title ?></em></h2>

<div class="container">
  <div class="row">
    <?php foreach ($weather->consolidated_weather as $date_weather): ?>
      <div class="col">
        <div class="card text-center border-0">
          <img src="https://www.metaweather.com/static/img/weather/<?= $date_weather->weather_state_abbr ?>.svg" width="75" class="m-auto">
          <div class="card-body">
            <h5 class="card-title"><?= $date_weather->applicable_date ?></h5>
            <p class="card-text"><?= round($date_weather->the_temp) ?>&deg; | <?= $date_weather->weather_state_name ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
