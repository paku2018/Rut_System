var qrcode = new QRCode(document.getElementById("qrcode"), {
    text: menuLink,
    width: 300,
    height: 300,
    colorDark : "#000000",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H
});
