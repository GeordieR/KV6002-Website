<?php require_once('header.php'); ?>

<!-- Login Form -->
<form action="actions.php?type=login" method="post">
    <div class="mb-3 row">
        <label for="email" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="email" name="email">
        </div>
    </div>
    <div class="mb-3 row">
        <label for="password" class="col-sm-2 col-form-label">Password</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" id="password" name="password">
        </div>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Sign in</button>
    </div>
</form>

<?php


require_once('footer.php'); ?>