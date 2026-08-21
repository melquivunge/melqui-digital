module.exports = function (grunt) {
  grunt.initConfig({
    sass: {
      options: {
        implementation: require('sass'),
        // ponytail: no source maps in the built file — style.css ships to prod
        sourceMap: false,
        style: 'expanded',
      },
      dist: { files: { 'assets/css/main.css': 'src/scss/main.scss' } },
    },

    postcss: {
      options: {
        processors: [
          require('autoprefixer')(),
          require('cssnano')({ preset: ['default', { discardComments: { removeAll: true } }] }),
        ],
      },
      dist: { src: 'assets/css/main.css' },
    },

    uglify: {
      options: { compress: { drop_console: true }, mangle: true },
      dist: { files: { 'assets/js/main.js': ['src/js/main.js'] } },
    },

    watch: {
      css: { files: ['src/scss/**/*.scss'], tasks: ['sass', 'postcss'] },
      js: { files: ['src/js/**/*.js'], tasks: ['uglify'] },
    },
  });

  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('@lodder/grunt-postcss');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('build', ['sass', 'postcss', 'uglify']);
  grunt.registerTask('default', ['build']);
};
