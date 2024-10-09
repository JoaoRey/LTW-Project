<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/item.class.php');
require_once(__DIR__ . '/../database/user.class.php');

function drawLoginForm(Session $session, $db) { ?>
    <main>
        <section id="login">
            <h1>Login</h1>
            <form class="form-log" action="../actions/action_login.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">

                <label>
                    Email <input type="email" name="email" required title="Please enter a valid email address.">
                </label>
                <label>
                    Password
                    <div class="password-wrapper">
                        <input type="password" name="password" id="login-password" required pattern="^(?=.*[!@#$%^&*?])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*?]{6,}$" title="Password should be at least 6 characters long and contain at least one special character, one uppercase letter, and may contain the '?' symbol.">
                        <span class="toggle-password" onclick="togglePasswordVisibility('login-password')">
                            <svg class="eye-open" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 12C1 12 5 3 12 3C19 3 23 12 23 12C23 12 19 21 12 21C5 21 1 12 1 12Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg class="eye-closed" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" xmlns="http://www.w3.org/2000/svg" style="display:none;">
                                <path d="M1 12C1 12 5 3 12 3C19 3 23 12 23 12C23 12 19 21 12 21C5 21 1 12 1 12Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="1" y1="1" x2="23" y2="23" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                </label>
                <div id="flex-login-regis">
                    <button type="submit">Login</button>
                    <a href="register.php">Register</a>
                </div>
            </form>
        </section>
    </main>
<?php } ?>

<?php function drawRegisterForm(Session $session, $db) { ?>
<main>
    <section id="login">
        <h1>Register</h1>
        <form class="form-log" action="/actions/action_register.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($session->getCsrfToken()) ?>">

            <h1>User</h1>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required title="Please enter a valid email address.">
            </div>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required pattern="^[a-zA-Z0-9_]{3,20}$" title="Username should be 3-20 characters long and can include letters, numbers, and underscores.">
            </div>
            <div class="name-group">
                <div class="input-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="firstName" required pattern="^[a-zA-ZÀ-ÖØ-öø-ÿ\s'-]{1,50}$" title="First name should only contain letters and be 1-50 characters long.">
                </div>
                <div class="input-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="lastName" required pattern="^[a-zA-ZÀ-ÖØ-öø-ÿ\s'-]{1,50}$" title="Last name should only contain letters and be 1-50 characters long.">
                </div>
            </div>
            <div class="location-group">
                <h1>Location</h1>
                <div class="input-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" pattern="^[a-zA-Z0-9À-ÖØ-öø-ÿ\s,º.'-]{1,100}$" title="Address can contain letters, numbers, and symbols like comma, dot, apostrophe, and hyphen.">
                </div>
                <div class="input-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" pattern="^[a-zA-ZÀ-ÖØ-öø-ÿ\s'-]{1,50}$" title="City should only contain letters and be 1-50 characters long.">
                </div>
                <div class="input-group">
                    <label for="district">District</label>
                    <input type="text" id="district" name="district" pattern="^[a-zA-ZÀ-ÖØ-öø-ÿ\s'-]{1,50}$" title="District should only contain letters and be 1-50 characters long.">
                </div>
                <div class="input-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" pattern="^[a-zA-ZÀ-ÖØ-öø-ÿ\s'-]{1,50}$" title="Country should only contain letters and be 1-50 characters long.">
                </div>
                <div class="input-group">
                    <label for="postal-code">Postal Code</label>
                    <input type="text" id="postal-code" name="postalCode" pattern="^\d{4}-\d{3}$" title="Postal code should be in the format dddd-ddd.">
                </div>
                <div class="input-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" pattern="^\+?\d{9,12}$" title="Phone number should be 9 digits long, or up to 12 digits if including an optional leading +.">
                </div>
            </div>
            <div class="password-group">
                <h1>Password</h1>
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required pattern="^(?=.*[!@#$%^&*?])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*?]{6,}$" title="Password should be at least 6 characters long and contain at least one special character, one uppercase letter, and may contain the '?' symbol.">
                        <span class="toggle-password" onclick="togglePasswordVisibility('password')">
                            <svg class="eye-open" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 12C1 12 5 3 12 3C19 3 23 12 23 12C23 12 19 21 12 21C5 21 1 12 1 12Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg class="eye-closed" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" xmlns="http://www.w3.org/2000/svg" style="display:none;">
                                <path d="M1 12C1 12 5 3 12 3C19 3 23 12 23 12C23 12 19 21 12 21C5 21 1 12 1 12Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="1" y1="1" x2="23" y2="23" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="input-group">
                    <label for="repeat-password">Repeat Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="repeat-password" name="repeat_password" required pattern="^(?=.*[!@#$%^&*?])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*?]{6,}$" title="Password should be at least 6 characters long and contain at least one special character, one uppercase letter, and may contain the '?' symbol.">
                        <span class="toggle-password" onclick="togglePasswordVisibility('repeat-password')">
                            <svg class="eye-open" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 12C1 12 5 3 12 3C19 3 23 12 23 12C23 12 19 21 12 21C5 21 1 12 1 12Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg class="eye-closed" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" xmlns="http://www.w3.org/2000/svg" style="display:none;">
                                <path d="M1 12C1 12 5 3 12 3C19 3 23 12 23 12C23 12 19 21 12 21C5 21 1 12 1 12Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="1" y1="1" x2="23" y2="23" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
            </div>

            <div id="flex-login-regis">
                <button type="submit">Register</button>
                <a href="login.php">Login</a>
            </div>
        </form>
    </section>
</main>
<?php } ?>
