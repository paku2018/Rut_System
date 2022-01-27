console.log('se cargan las js para el manejo de impresion de tickets');
var _pagocash_NamePrinterTickets = null;
var _pagocash_UrlLastTicketPrinted = null;

//WebSocket settings
JSPM.JSPrintManager.license_url = window.location.origin+"/jspm";
JSPM.JSPrintManager.auto_reconnect = true;
JSPM.JSPrintManager.start();
JSPM.JSPrintManager.WS.onStatusChanged = function () {
    if (jspmWSStatus()) {
        //get client installed printers
        JSPM.JSPrintManager.getPrinters().then(function (myPrinters) {
            var options = '';
            for (var i = 0; i < myPrinters.length; i++) {
                if(i==0){
                    _pagocash_NamePrinterTickets = myPrinters[i];
                }
                console.log('IMPRESORA',myPrinters[i]);
            }
        });
    }
};

//Check JSPM WebSocket status
function jspmWSStatus() {
    var icon_print_active = document.getElementById('sprint_active');
    var icon_print_inactive = document.getElementById('sprint_inactive');


    if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Open){
        icon_print_active.classList.remove('d-none');
        icon_print_inactive.classList.add('d-none');
        return true;
    }else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Closed) {
        icon_print_active.classList.add('d-none');
        icon_print_inactive.classList.remove('d-none');
        console.log('JSPrintManager (JSPM) is not installed or not running! Download JSPM Client App from https://neodynamic.com/downloads/jspm');
        return false;
    }
    else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Blocked) {
        console.log('JSPM has blocked this website!');
        icon_print_active.classList.add('d-none');
        icon_print_inactive.classList.remove('d-none');
        return false;
    }
}

function doPrinting(url_print_file) {
    if (jspmWSStatus()) {
        _pagocash_UrlLastTicketPrinted = url_print_file;
        ImprimirSilenciosamenteV2(url_print_file);
    }
}

function doPrintingPDF(url_print_file) {
    if (jspmWSStatus()) {
        _pagocash_UrlLastTicketPrinted = url_print_file;
        ImprimirSilenciosamentePDF(url_print_file);
    }
}

function ImprimirSilenciosamenteV2(url_print_file){
    //create ClientPrintJob
    var cpj = new JSPM.ClientPrintJob();
        cpj.clientPrinter = new JSPM.DefaultPrinter();


    //Set Image file
    var my_file = null;
    var file_name;
    var file_ext;

    if (url_print_file.length > 0) {
        var file_url = url_print_file;
        file_ext = file_url.substring(file_url.lastIndexOf('.'));
        file_name = file_url.substring(file_url.lastIndexOf('/') + 1, file_url.lastIndexOf('.'));
        file_name += file_ext;
        console.log(file_name);
        my_file = new JSPM.PrintFile( url_print_file , JSPM.FileSourceType.URL, file_name, 1);
    } else {
        //alert('Debe especificar un archivo local o una URL para imprimir!');
        alert('Ticket para impresion no encontrado');
        return;
    }

    //add file to ClientPrintJob
    cpj.files.push(my_file);

    //Send print job to printer!
    cpj.sendToClient();
}

function ImprimirSilenciosamentePDF(url_print_file){
    var cpj = new JSPM.ClientPrintJob();
        cpj.clientPrinter = new JSPM.DefaultPrinter();

    //Set Image file
    var my_file = null;
    var file_name;
    var file_ext;

    if (url_print_file.length > 0) {
        var file_url = url_print_file;
        file_ext = file_url.substring(file_url.lastIndexOf('.'));
        file_name = file_url.substring(file_url.lastIndexOf('/') + 1, file_url.lastIndexOf('.'));
        file_name += file_ext;
        console.log(file_name);
        //my_file = new JSPM.PrintFile( url_print_file , JSPM.FileSourceType.URL, file_name, 1);
        my_file = new JSPM.PrintFilePDF(url_print_file, JSPM.FileSourceType.URL, "BOLETA.pdf", 1)
    } else {
        //alert('Debe especificar un archivo local o una URL para imprimir!');
        alert('Ticket para impresion no encontrado');
        return;
    }

    //add file to ClientPrintJob
    cpj.files.push(my_file);

    //Send print job to printer!
    cpj.sendToClient();
}


function ReImprimirUltimoTicket(){
    if(_pagocash_UrlLastTicketPrinted){
        doPrinting(_pagocash_UrlLastTicketPrinted);
    }else{

    }
}


