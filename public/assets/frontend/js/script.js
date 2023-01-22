function displayCurrentTime() {
    let currentTime = new Date();
    let hours = currentTime.getHours();
    let minutes = currentTime.getMinutes();
    let seconds = currentTime.getSeconds();
    // let amOrPm = hours < 24 ? "AM" : "PM";

    hours = hours === 0 ? 24 : hours > 24 ? hours - 24 : hours;
    hours = addZero(hours);
    minutes = addZero(minutes);
    seconds = addZero(seconds);

    let timeString = "Pukul : " + `${hours} : ${minutes} : ${seconds} ` + "WIB";

    document.getElementById("time").innerText = timeString;
    let timer = setTimeout(displayCurrentTime, 1000);
}

function addZero(component) {
    return component < 10 ? "0" + component : component;
}

displayCurrentTime();


