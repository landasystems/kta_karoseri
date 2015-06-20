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
                                .state('app.m', {
                                    url: '/master',
                                    template: '<div ui-view class="fade-in"></div>',
                                })
                                .state('app.m.product', {
                                    url: '/product',
                                    templateUrl: 'tpl/m_product/index.html',
                                    resolve: {
                                        deps: ['uiLoad',
                                            function(uiLoad) {
                                                return uiLoad.load('js/controllers/productsCtrl.js');
                                            }]
                                    }
                                })
                                .state('app.m.belajar', {
                                    url: '/belajar',
                                    templateUrl: 'tpl/belajar.html',
                                    resolve: {
                                        deps: ['uiLoad',
                                            function(uiLoad) {
                                                return uiLoad.load('js/controllers/belajar.js');
                                            }]
                                    }
                                })
                                .state('app.m.belajarDet', {
                                    url: '/belajar/:id/:id2',
                                    templateUrl: 'tpl/belajarDet.html',
                                    resolve: {
                                        deps: ['uiLoad',
                                            function(uiLoad) {
                                                return uiLoad.load('js/controllers/belajar2.js');
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