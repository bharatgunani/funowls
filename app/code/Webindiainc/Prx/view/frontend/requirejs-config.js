var config = {
    map: {
        '*':{
                fancybox: 'Webindiainc_Prx/js/jquery.fancybox.min',
            	customjs: 'Webindiainc_Prx/js/custom'
         }
        },
    shim:{
     'fancybox':{
      deps: ['jquery']
      },	
     'customjs':{
      deps: ['jquery']
      }
     }
};