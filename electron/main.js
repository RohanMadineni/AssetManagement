const { app, BrowserWindow } = require('electron');
const path = require('path');

const isDev = !app.isPackaged;
const createWindow = () => {
    const win = new BrowserWindow({
        width: 1400,
        height: 900,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true
        }
    });
    if(isDev) {
        win.loadURL('http://localhost:4200');
        win.webContents.openDevTools();
    } 
    
    else {
        win.loadFile(path.join(__dirname, '../client-app/dist/client-app/browser/index.html'));
    }
    
}

app.whenReady().then(() => {
        createWindow()
    });

app.on('window-all-closed', () => {
    if(process.platform !== 'darwin') app.quit();
});
