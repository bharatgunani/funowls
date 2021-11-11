var config = {
    map: {
        '*':{
              customjs: 'js/product/custom',
              snapchatjs: 'js/snapchat'
         }
        },
    shim:{
     'customjs':{
      deps: ['jquery']
      },
     'snapchatjs':{
      deps: ['jquery']
      }
     }
};