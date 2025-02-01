module.exports = {
    content: [
      "./admin/templates/**/*.php",
      "./templates/**/*.php",  // Add this line
      "./assets/js/**/*.js",
      "./node_modules/flowbite/**/*.js"
    ],
    theme: {
      extend: {},
    },
    plugins: [
      require('flowbite/plugin')
    ],
}