/**
 * Easy Optix
 *
 * @author      Ievgen Borodin
 * @copyright   Copyright (c) 2016 EasyOptix Inc. (http://www.easy-optix.com)
 */

module.exports = function(grunt){

grunt.initConfig({
  sources: {
  	js: 'skin/frontend/easyoptix/default/js',
  	css: 'skin/frontend/easyoptix/default/css' 
  },	
  pkg: grunt.file.readJSON('package.json'),
  uglify: {
  	options: {
  	  banner: '/**\n * <%= pkg.company %>\n *\n * @author      <%= pkg.author %>\n ' +
        '* @copyright   <%= pkg.copyright %>\n */\n\n',
      mangle: {
        except: ['jQuery', 'Backbone']
      }
    },
    my_target: {
      files: {
        '<%= sources.js %>/scripts.min.js': ['<%= sources.js %>/scripts.js'],
        '<%= sources.js %>/jquery.easing.1.3.min.js': ['<%= sources.js %>/jquery.easing.1.3.js'],
        '<%= sources.js %>/jquery.carouFredSel-6.2.1.min.js': ['<%= sources.js %>/jquery.carouFredSel-6.2.1.js'],
        '<%= sources.js %>/jquery.touchSwipe.min.js': ['<%= sources.js %>/jquery.touchSwipe.js'],
        '<%= sources.js %>/tm-stick-up.min.js': ['<%= sources.js %>/tm-stick-up.js'],
        '<%= sources.js %>/bundle.min.js': ['<%= sources.js %>/bundle.js'],
        '<%= sources.js %>/camera.min.js': ['<%= sources.js %>/camera.js'],
        'js/sort/category.min.js': ['js/sort/category.js'],
        'js/ecommerceteam/cloud-zoom.1.0.2.min.js': ['js/ecommerceteam/cloud-zoom.1.0.2.js']
      }
    }
  },
  clean: {
    old_cssmin: ['<%= sources.css %>/style.min.css', '<%= sources.css %>/responsive.min.css', '<%= sources.css %>/photoswipe.min.css', '<%= sources.css %>/camera.min.css']
  },
  cssmin: {
	  options: {
	    shorthandCompacting: false,
	    roundingPrecision: -1
	  },
	  target: {
	    files: {
	      '<%= sources.css %>/styles.min.css': ['<%= sources.css %>/styles.css'],
	      '<%= sources.css %>/responsive.min.css': ['<%= sources.css %>/responsive.css'],
	      '<%= sources.css %>/photoswipe.min.css': ['<%= sources.css %>/photoswipe.css'],
	      '<%= sources.css %>/camera.min.css': ['<%= sources.css %>/camera.css']
	    }
	  }
	},
	watch: {
	  js: {
	    files: ['<%= sources.js %>/*.js', '<%= sources.js_skin %>/*.js'],
	    tasks: ['uglify']
	  },
	  css: {
	    files: ['<%= sources.css %>/*.css'],
	    tasks: ['cssmin']
	  },
	},
});

grunt.loadNpmTasks('grunt-contrib-clean');
grunt.loadNpmTasks('grunt-contrib-uglify');
grunt.loadNpmTasks('grunt-contrib-cssmin');
grunt.loadNpmTasks('grunt-contrib-watch');

grunt.registerTask('default', ['uglify', 'clean', 'cssmin']);//, 'watch'

};