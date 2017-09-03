<?php include_once "common/header.php"; ?>

<div id="main">

    <noscript>This site just doesn't work, period, without JavaScript</noscript>

    <!-- IF LOGGED IN -->

        <!-- Content here -->
        <ul id="mealplan">
            <li class="day">
                <ul class="dayplan">
                    <li class="breakfast">
                        <span>Huevos Rancheros</span>
                        <div class="edittab tab"></div>
                    </li>

                    <li class="lunch">
                        <span>French Dip</span>
                        <div class="edittab tab"></div>
                    </li>

                    <li class="dinner">
                        <span>Kale Pesto</span>
                        <div class="edittab tab"></div>
                    </li>

                    <li class="snack">
                        <span>Flax Crackers</span>
                        <div class="edittab tab"></div>
                    </li>
                </ul>
            </li>
        </ul>

        <form action="" id="create-new">
            <div>
                <input type="text" id="new-meal-plan-name-text" name="new-meal-plan-name-text" />
                <input type="number" id="new-meal-plan-length" name="new-meal-plan-length" min="1" max="4"/>
                <input type="checkbox" id="new-meal-plan-include-breakfast" name="new-meal-plan-include-breakfast" />
                <input type="checkbox" id="new-meal-plan-include-lunch" name="new-meal-plan-include-lunch" />
                <input type="checkbox" id="new-meal-plan-include-dinner" name="new-meal-plan-include-dinner" />
                <input type="checkbox" id="new-meal-plan-include-snack" name="new-meal-plan-include-snack" />
                <input type="radio" id="new-meal-plan-recipe-creator" name="new-meal-recipe-creator" value="user"/>
                <input type="radio" id="new-meal-plan-recipe-creator" name="new-meal-recipe-creator" value="mpom"/>
                <input type="radio" id="new-meal-plan-recipe-creator" name="new-meal-recipe-creator" value="user-and-mpom"/>
                <input type="radio" id="new-meal-plan-recipe-creator" name="new-meal-recipe-creator" value="all"/>
                <input type="submit" id="create-new-submit" value="Create" class="button" />
            </div>

        </form>

        <div id="share-area">
            <p>Public list URL: <a href="#">URL GOES HERE</a>
            <small>(Nobody but YOU will be able to edit this list)</small></p>
        </div>


    <!-- IF LOGGED OUT -->

        <!-- Alternate content here -->

        <ul id="mealplan">
            <li class="day">
                <ul class="dayplan">
                    <li class="breakfast">
                        <span>Huevos Rancheros</span>
                    </li>

                    <li class="lunch">
                        <span>French Dip</span>
                    </li>

                    <li class="dinner">
                        <span>Kale Pesto</span>
                    </li>

                    <li class="snack">
                        <span>Flax Crackers</span>
                    </li>
                </ul>
            </li>
        </ul>

        <img src="/images/newlist.jpg" alt="Your automatic mealplan here!" />

</div>

<?php include_once "common/sidebar.php"; ?>

<?php include_once "common/footer.php"; ?>
