<div class="wrap">
  <div id="poststuff">
    <h1>API Keys</h1>
    <p>Developers can use API keys to grant limited access to the Disciple Tools
      API from external websites and applications</p>

    <form action="" method="post">
      <h2>Token Generator</h2>
      <table class="widefat striped" style="margin-bottom:50px">
        <tr>
          <th>
            <label for="application">Application Name</label>
          </th>
          <td>
            <input type="text" id="application" name="application">
            <button type="submit" class="button">Generate Token</button>
          </td>
        </tr>
      </table>
    </form>
    <h2>Existing Keys</h2>
    <table class="widefat striped">
      <thead>
      <tr>
        <th>Client ID</th>
        <th>Client Token</th>
      </tr>
      </thead>
      <?php foreach ( $keys as $key ): ?>
        <tbody>
        <tr>
          <td>
        <?php echo $key["client_id"] ?>
          </td>
          <td>
        <?php echo $key["client_token"] ?>
          </td>
        </tr>
        </tbody>
      <?php endforeach; ?>
    </table>
  </div>
</div>