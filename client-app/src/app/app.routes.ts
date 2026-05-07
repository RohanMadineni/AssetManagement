import { Routes } from '@angular/router';
import { LayoutPage } from './layout/layout';
import { LoginPage } from './login/login';
import { authGuard } from './auth-guard';
import { DashboardPage } from './dashboard/dashboard';
import { AssetListPage } from './asset-list/asset-list';
import { SystemPage } from './system/system';
import { AddAssetComponent } from './add-asset/add-asset';
import { AssetDetail } from './asset-detail/asset-detail';
export const routes: Routes = [
    {
        path: 'login',
        component: LoginPage,
        title: 'Login Page',
    },

    

    {
        path: '',
        component: LayoutPage,
        canActivate: [authGuard],
        children : [
            // {path: 'dashboard', component: DashboardPage, canActivate: [authGuard]},
            {path: 'system', loadComponent: ()=> import('./system/system').then(m=>m.SystemPage)},
            {path: 'dashboard', loadComponent: ()=> import('./dashboard/dashboard').then(m => m.DashboardPage)},
            {path: 'assets', component: AssetListPage, canActivate: [authGuard]},
            {path: 'addAsset', component: AddAssetComponent, canActivate: [authGuard]},
            {path: 'assetdetail', component: AssetDetail, canActivate: [authGuard]},
            {path: 'register', loadComponent: () => import('./register/register').then(m => m.RegisterPage),},
            {path: 'categoryConfig', loadComponent: () => import('./category-config/category-config').then(m => m.CategoryConfig),},
            {path: 'usermanagement', loadComponent: () => import('./user-management/user-management').then(m => m.UserManagement),},
            {path: 'availableAssets', loadComponent: () => import('./available-assets-list/available-assets-list').then(m => m.AvailableAssetsList),},
        ]
    },

    {
        path: '**',
        redirectTo: 'login',
        
    },
];