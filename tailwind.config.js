const { colors } = require('tailwindcss/defaultTheme');

module.exports = {
  theme: {
    extend: {},
    colors: {
      ...colors,
      primary: colors.indigo
    }
  },
  variants: {
    borderWidth: ['responsive', 'last', 'hover', 'focus'],
  },
  plugins: [],
}
