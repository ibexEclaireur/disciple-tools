<div class="wrap">
  <div id="poststuff">
    <h1>API Keys</h1>
    <p>Developers can use API keys to grant limited access to the Disciple Tools
      API from external websites and applications. To get an API key, fill in what what you want to call it below.
      We will generate Client Token and Client Id base on the name.
    </p>

      <form action="" method="post">
        <h2>Token Generator</h2>
        <table class="widefat striped" style="margin-bottom:50px">
          <tr>
            <th>
              <label for="application">Name</label>
            </th>
            <td>
              <input type="text" id="application" name="application">
              <button type="submit" class="button">Generate Token</button>
            </td>
          </tr>
        </table>
      <h2>Existing Keys</h2>
      <table class="widefat striped">
        <thead>
        <tr>
          <th>Client ID</th>
          <th>Client Token</th>
            <th></th>
        </tr>
        </thead>
        <?php foreach ( $keys as $id => $key): ?>
          <tbody>
          <tr>
            <td>
            <?php echo $key["client_id"] ?>
            </td>
            <td>
            <?php echo $key["client_token"] ?>
            </td>
            <td>
              <button type="submit" class="button button-delete" name="delete" value="<?php echo $id?>">Delete <?php echo $id?></button>
            </td>
          </tr>
          </tbody>
        <?php endforeach; ?>
      </table>
    </form>
  </div>
</div>
