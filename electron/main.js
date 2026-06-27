const { app, BrowserWindow } = require('electron');
const { autoUpdater } = require('electron-updater');
const path = require('path');
const log = require ('electron-log');

autoUpdater.logger = log;
autoUpdater.logger.transports.file.level = 'info';
log.info('App starting...');

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
        createWindow();
        autoUpdater.checkForUpdatesAndNotify();
    });

app.on('window-all-closed', () => {
    if(process.platform !== 'darwin') app.quit();
});

autoUpdater.on('update-available', () => {
    console.log('Update available');
});

autoUpdater.on('update-downloaded', () => {
    console.log('Update downloaded, restarting...');

    autoUpdater.quitAndInstall();
})