'use strict';

/**
 * Config for the router
 */
angular.module('app')
        .run(
        ['$rootScope', '$state', '$stateParams',
            function($rootScope, $state, $stateParams) {
                $rootScope.$state = $state;
                $rootScope.$stateParams = $stateParams;
            }
        ]
        )
        .config(
        ['$stateProvider', '$urlRouterProvider',
            function($stateProvider, $urlRouterProvider) {

                $urlRouterProvider
                        .otherwise('/app/dashboard');
                $stateProvider
                        .state('app', {
                    abstract: true,
                    url: '/app',
                    templateUrl: 'tpl/app.html'
                })
                        .state('app.dashboard', {
                    url: '/dashboard',
                    templateUrl: 'tpl/dashboard.html',
                    resolve: {
                        deps: ['$ocLazyLoad',
                            function($ocLazyLoad) {

                                            }]
                                    }
                                })
                                //master roles
                                .state('master', {
                                    url: '/master',
                                    templateUrl: 'tpl/app.html'
                                })
                                .state('master.barang', {
                                    url: '/master/barang',
                                    templateUrl: 'tpl/m_barang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/barang.js');
                                            }]
                                    }
                                })
                                .state('master.jenisbrg', {
                                    url: '/master/jenisbrg',
                                    templateUrl: 'tpl/m_jenisbrg/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/jenisbrg.js');
                                            }]
                                    }
                                })
                                // others
                                .state('access', {
                                    url: '/access',
                                    template: '<div ui-view class="fade-in-right-big smooth"></div>'
                                })
                                .state('access.signin', {
                                    url: '/signin',
                                    templateUrl: 'tpl/page_signin.html',
                                    resolve: {
                                        deps: ['uiLoad',
                                            function(uiLoad) {
                                                return uiLoad.load(['js/controllers/signin.js']);
                                            }]
                                    }
                                })
                                .state('access.404', {
                                    url: '/404',
                                    templateUrl: 'tpl/page_404.html'
                                })
                    }
                })
                        //master roles
                        .state('master', {
                    url: '/master',
                    templateUrl: 'tpl/app.html'
                })
                        .state('master.jenisbrg', {
                    url: '/master/jenisbrg',
                    templateUrl: 'tpl/m_jenisbrg/index.html',
                    resolve: {
                        deps: ['$ocLazyLoad',
                            function($ocLazyLoad) {
                                return $ocLazyLoad.load('js/controllers/jenisbrg.js');
                            }]
                    }
                })
                //kendaraan
                        .state('master.chassis', {
                    url: '/master/chassis',
                    templateUrl: 'tpl/m_chassis/index.html',
                    resolve: {
                        deps: ['$ocLazyLoad',
                            function($ocLazyLoad) {
                                return $ocLazyLoad.load('js/controllers/chassis.js');
                            }]
                    }
                })
                        // others
                        .state('access', {
                    url: '/access',
                    template: '<div ui-view class="fade-in-right-big smooth"></div>'
                })
                        .state('access.signin', {
                    url: '/signin',
                    templateUrl: 'tpl/page_signin.html',
                    resolve: {
                        deps: ['uiLoad',
                            function(uiLoad) {
                                return uiLoad.load(['js/controllers/signin.js']);
                            }]
                    }
                })
                        .state('access.404', {
                    url: '/404',
                    templateUrl: 'tpl/page_404.html'
                })
            }
        ]
        );
