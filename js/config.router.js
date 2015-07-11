'use strict';

/**
 * Config for the router
 */
angular.module('app')
        .run(
                ['$rootScope', '$state', '$stateParams', 'Data',
                    function ($rootScope, $state, $stateParams, Data) {
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
//                                .state('access.forbidden', {
//                                    url: '/forbidden',
//                                    templateUrl: 'tpl/page_forbidden.html'
//                                })
                                .state('access.404', {
                                    url: '/404',
                                    templateUrl: 'tpl/page_404.html'
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
                                // kalender
                                .state('master.kalender', {
                                    url: '/kalender',
                                    templateUrl: 'tpl/m_kalender/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/kalender.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                // lokasi
                                .state('master.lokasi', {
                                    url: '/lokasi-kantor',
                                    templateUrl: 'tpl/m_lokasikantor/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {

                                                return $ocLazyLoad.load('js/controllers/lokasikantor.js');

                                            }]
                                    }
                                })
                                // jabatan
                                .state('master.jabatan', {
                                    url: '/jabatan',
                                    templateUrl: 'tpl/m_jabatan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['ui.select2']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/jabatan.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                // subsection
                                .state('master.subsection', {
                                    url: '/subsection',
                                    templateUrl: 'tpl/m_subsection/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['ui.select2']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/subsection.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                // subsection
                                .state('master.section', {
                                    url: '/section',
                                    templateUrl: 'tpl/m_section/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['ui.select2']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/section.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                // umk
                                .state('master.umk', {
                                    url: '/umk',
                                    templateUrl: 'tpl/m_umk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {

                                                return $ocLazyLoad.load('js/controllers/umk.js');

                                            }]
                                    }
                                })
                                // departement
                                .state('master.departement', {
                                    url: '/department',
                                    templateUrl: 'tpl/m_department/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {

                                                return $ocLazyLoad.load('js/controllers/departement.js');

                                            }]
                                    }
                                })

                                // user
                                .state('master.user', {
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
                                    url: '/roles',
                                    templateUrl: 'tpl/m_roles/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/roles.js');
                                            }]
                                    }})
                                // Transaksi
                                .state('transaksi', {
                                    url: '/transaksi',
                                    templateUrl: 'tpl/app.html'
                                })
                                //BOM
                                .state('transaksi.bom', {
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
                                .state('transaksi.rubah-bentuk', {
                                    url: '/rubah-bentuk',
                                    templateUrl: 'tpl/t_rubahbentuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['ui.select2', 'daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/rubahbentuk.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //BSTK
                                .state('transaksi.bstk', {
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
                                .state('transaksi.sti', {
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
                                .state('transaksi.validasibom', {
                                    url: '/validasibom',
                                    templateUrl: 'tpl/t_validasibom/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/validasibom.js');
                                            }]
                                    }})
                                // SPK
                                .state('transaksi.spk', {
                                    url: '/spk',
                                    templateUrl: 'tpl/t_spk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('ui.select2').then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/spk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.s-pesanankaroseri', {
                                    url: '/suratpesanankaroseri',
                                    templateUrl: 'tpl/t_s-pesanankaroseri/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/s-pesanankaroseri.js');
                                            }]
                                    }})
                                //
                                //
                                .state('transaksi.bkt-barangkeluar', {
                                    url: '/bkt-barangkeluar',
                                    templateUrl: 'tpl/t_bkt-barangkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/bkt-barangkeluar.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.bkt-barangmasuk', {
                                    url: '/bkt-barangmasuk',
                                    templateUrl: 'tpl/t_bkt-barangmasuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/bkt-barangmasuk.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.bukti-terima', {
                                    url: '/bukti-terima',
                                    templateUrl: 'tpl/t_bukti-terima/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/bkt-terima.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.pembatalanchasis', {
                                    url: '/pembatalanchassis',
                                    templateUrl: 'tpl/t_pembatalanchassis/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/pembatalanchassis.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.claimunit', {
                                    url: '/claimunit',
                                    templateUrl: 'tpl/t_claimunit/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['ui.select2', 'daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/claimunit.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.deliveryunit', {
                                    url: '/deliveryunit',
                                    templateUrl: 'tpl/t_deliveryunit/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload', 'ui.select2']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/deliveryunit.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.kpb', {
                                    url: '/kpb',
                                    templateUrl: 'tpl/t_kpb/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/kpb.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.purchase-order', {
                                    url: '/purchase-order',
                                    templateUrl: 'tpl/t_purchase-order/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['ui.select']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/purchase-order.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.retur-buktibarangkeluar', {
                                    url: '/retur-buktibarangkeluar',
                                    templateUrl: 'tpl/t_retur-buktibarangkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/retur-buktibarangkeluar.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.spp-nonrutin', {
                                    url: '/spp-nonrutin',
                                    templateUrl: 'tpl/t_spp-nonrutin/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/spp-nonrutin.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.spp-rutin', {
                                    url: '/spp-nonrutin',
                                    templateUrl: 'tpl/t_spp-rutin/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/spp-rutin.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.tambahitem', {
                                    url: '/tambahitem',
                                    templateUrl: 'tpl/t_tambahitem/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/tambahitem.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.ujimutu', {
                                    url: '/ujimutu',
                                    templateUrl: 'tpl/t_ujimutu/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['ui.select2']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/ujimutu.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.w-inprogress', {
                                    url: '/workinprogress',
                                    templateUrl: 'tpl/t_w-inprogress/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/w-inprogress.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.w-orderkeluar', {
                                    url: '/workorderkeluar',
                                    templateUrl: 'tpl/t_w-orderkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/w-orderkeluar.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.w-ordermasuk', {
                                    url: '/workordermasuk',
                                    templateUrl: 'tpl/t_w-ordermasuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/w-ordermasuk.js');
                                            }]
                                    }})

                    }
                ]);

