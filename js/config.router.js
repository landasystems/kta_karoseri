'use strict';

/**
 * Config for the router
 */
angular.module('app')
        .run(
                ['$rootScope', '$state', '$stateParams',
                    function ($rootScope, $state, $stateParams) {
                        $rootScope.$state = $state;
                        $rootScope.$stateParams = $stateParams;

                        //pengecekan login
                        $rootScope.$on("$stateChangeStart", function (event, toState) {
                            var globalmenu = ['app.dashboard'];
                            Data.get('site/session').then(function (results) {
                                if (typeof results.data.user != "undefined") {
                                    $rootScope.user = results.data.user;
                                    if (results.data.user.akses[(toState.name).replace(".", "_")]) { // jika punya hak akses, return true

                                    } else {
                                        if (globalmenu.indexOf(toState.name) >= 0) { //menu global menu tidak di redirect

                                        } else {
                                            $state.go("access.forbidden");
                                        }
                                    }
                                } else {
                                    $state.go("access.signin");
                                }
                            });
                        });
                    }
                ]
                )
        .config(
                ['$stateProvider', '$urlRouterProvider',
                    function ($stateProvider, $urlRouterProvider) {

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
                                            function ($ocLazyLoad) {

                                            }]
                                    }
                                })
                                //master roles
                                .state('master', {
                                    url: '/master',
                                    templateUrl: 'tpl/app.html'
                                })
                                .state('master.barang', {
                                    url: '/barang',
                                    templateUrl: 'tpl/m_barang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload', 'ui.select2']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/barang.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //jenis barang
                                .state('master.jenisbrg', {
                                    url: '/jenisbrg',
                                    templateUrl: 'tpl/m_jenisbrg/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/jenisbrg.js');
                                            }]
                                    }
                                })
                                .state('master.customer', {
                                    url: '/customer',
                                    templateUrl: 'tpl/m_customer/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/customer.js');
                                            }]
                                    }
                                })
                                .state('master.modelkendaraan', {
                                    url: '/modelkendaraan',
                                    templateUrl: 'tpl/m_modelkendaraan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/modelkendaraan.js');
                                            }]
                                    }
                                })
                                .state('master.jnskomplain', {
                                    url: '/jnskomplain',
                                    templateUrl: 'tpl/m_jnskomplain/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/jnskomplain.js');
                                            }]
                                    }
                                })
                                .state('master.chassis', {
                                    url: '/chassis',
                                    templateUrl: 'tpl/m_chassis/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/chassis.js');
                                            }]
                                    }
                                })
                                // supplier
                                .state('master.supplier', {
                                    url: '/supplier',
                                    templateUrl: 'tpl/m_supplier/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/supplier.js');
                                            }]
                                    }
                                })
                                // user
                                .state('master.pengguna', {
                                    url: '/pengguna',
                                    templateUrl: 'tpl/m_user/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/pengguna.js');
                                            }]
                                    }
                                })
                                .state('master.roles', {
                                    url: '/master/roles',
                                    templateUrl: 'tpl/m_roles/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/roles.js');
                                            }]
                                    }})
                                // Transaksi
                                .state('trans', {
                                    url: '/trans',
                                    templateUrl: 'tpl/app.html'
                                })
                                //BOM
                                .state('trans.bom', {
                                    url: '/bom',
                                    templateUrl: 'tpl/bom/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload', 'ui.select2']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/bom.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //Rubah Bentuk
                                .state('trans.rubah-bentuk', {
                                    url: '/rubah-bentuk',
                                    templateUrl: 'tpl/rubah-bentuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['ui.select2']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/rubahbentuk.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //BSTK
                                .state('trans.bstk', {
                                    url: '/bstk',
                                    templateUrl: 'tpl/t_bstk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload', 'ui.select2']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/bstk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('trans.sti', {
                                    url: '/sti',
                                    templateUrl: 'tpl/t_sti/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload', 'ui.select2']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/sti.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //Validasi bom
                                .state('trans.validasibom', {
                                    url: '/validasibom',
                                    templateUrl: 'tpl/t_validasibom/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/validasibom.js');
                                            }]
                                    }})
                                // SPK
                                .state('trans.spk', {
                                    url: '/spk',
                                    templateUrl: 'tpl/t_spk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/spk.js');
                                            }]
                                    }})
                                //
                                .state('trans.bkt-barangkeluar', {
                                    url: '/bkt-barangkeluar',
                                    templateUrl: 'tpl/t_bkt-barangkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/bkt-barangkeluar.js');
                                            }]
                                    }})
                                 //
                                .state('trans.bkt-barangmasuk', {
                                    url: '/bkt-barangmasuk',
                                    templateUrl: 'tpl/t_bkt-barangmasuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/bkt-barangmasuk.js');
                                            }]
                                    }})
                                 //
                                .state('trans.bukti-terima', {
                                    url: '/bukti-terima',
                                    templateUrl: 'tpl/t_bukti-terima/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/bkt-bukti-terima.js');
                                            }]
                                    }})
                                 //
                                .state('trans.pembatalanchasis', {
                                    url: '/pembatalanchasis',
                                    templateUrl: 'tpl/t_pembatalanchasis/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/pembatalanchasis.js');
                                            }]
                                    }})
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
                                            function (uiLoad) {
                                                return uiLoad.load(['js/controllers/signin.js']);
                                            }]
                                    }
                                })
                                .state('access.404', {
                                    url: '/404',
                                    templateUrl: 'tpl/page_404.html'
                                })
                    }
                ]);

