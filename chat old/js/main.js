particlesJS.load('particles-js', './particles/particles.json', function() {
    //console.log('callback - particles.js config loaded');
});
$(document).ready(function() {

    console.log('%c Why are you here in the console?', 'background: #000; color: #ffa500');
    console.log('%c Dont try anything weird', 'background: #000; color: #ffa500');
    console.log("%c                                      \n    .->                .->            \n ,---(`-')   .---.(`-')----.  .----.  \n'  .-(OO )  / .  |( OO).-.  '\\_,-.  | \n|  | .-, \\ / /|  |( _) | |  |   .' .' \n|  | '.(_// '-'  ||\\|  |)|  | .'  /_  \n|  '-'  | `---|  |' '  '-'  '|      | \n `-----'      `--'   `-----' `------' ", 'background: #000; color: #ffa500')


    setTimeout(function() {
        if (document.getElementsByClassName('popup-msg').length >= 1) {
            document.querySelector('.popup-msg').style.display = "none";
        }
        if (document.getElementsByClassName('error').length >= 1) {
            document.querySelector('.error').style.display = "none";
        }
    }, 2200);
})

function closeProfile() {
    document.getElementById("profile").style.display = 'none';
    document.getElementById("close-btn").style.display = 'none';
    document.getElementById("close-btn-two").style.display = 'block';
}

function openProfile() {
    document.getElementById("profile").style.display = 'block';
    document.getElementById("close-btn").style.display = 'block';
    document.getElementById("close-btn-two").style.display = 'none';
}

function openNav() {
    document.getElementById("mySidenav").style.width = "100%"/*"250px"*/;
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}