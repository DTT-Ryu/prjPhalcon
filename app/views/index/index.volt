<h2 class="mb-4 text-center mt-2">Login</h2>
<form method="post" action="/index/login" class="mx-auto" style="max-width: 400px;">
    <div class="mb-3">
        <label for="loginEmail" class="form-label">Email</label>
        <input type="email" class="form-control" name="loginEmail" id="loginEmail" placeholder="Enter your email">
    </div>
    <div class="mb-3">
        <label for="loginPass" class="form-label">Password</label>
        <input type="password" class="form-control" name="loginPass" id="loginPass" placeholder="Enter your password">
    </div>
    <button type="submit" class="btn btn-primary w-100">Login</button>
</form>