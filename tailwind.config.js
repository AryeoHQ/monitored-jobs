module.exports = {
  mode: 'jit',
  purge: [
    './resources/**/*.{blade.php,vue}',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      scale: {
        '-1': '-1'
      }
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
