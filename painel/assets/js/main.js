//Dropdown Notifications
let notificationsWrap = document.getElementById("notificationsWrap");

function toggleNotifications()
{
    notificationsWrap.classList.toggle("open-notifications");
    userWrap.classList.remove("open-user");
}

//Dropdown User
let userWrap = document.getElementById("userWrap");

function toggleUser()
{
    userWrap.classList.toggle("open-user");
    notificationsWrap.classList.remove("open-notifications");
}

//Dark Mode Button
var themeCheckbox = document.getElementById('theme-checkbox');
var themeToggle = document.getElementById('theme-toggle');

if (themeCheckbox.checked)
{
    themeToggle.classList.add('toggle-dark-theme');
}
function themeColor() {
    if (themeCheckbox.checked)
    {
        themeToggle.classList.add('toggle-dark-theme');
    }
    else
    {
        themeToggle.classList.remove('toggle-dark-theme');
    }
}