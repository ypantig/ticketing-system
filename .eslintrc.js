module.exports = {
  "root": true,
  "extends": "eslint:recommended",
  "globals": {
    "wp": true
  },
  "env": {
    "node": true,
    "es6": true,
    "amd": true,
    "browser": true,
    "jquery": true
  },
  "parserOptions": {
    "parser":"babel-eslint",
    "ecmaFeatures": {
      "globalReturn": true,
      "generators": false,
      "objectLiteralDuplicateProperties": false,
      "experimentalObjectRestSpread": true
    },
    "ecmaVersion": 2017,
    "sourceType": "module"
  },
  "plugins": [
    "import",
    "vue",
    "html"
  ],
  "settings": {
    "import/core-modules": [],
    "html/html-extensions": [
      ".html",
      ".vue"
    ],  // consider .html and .we files as HTML
    "import/ignore": [
      "node_modules",
      "\\.(coffee|scss|css|less|hbs|svg|json)$"
    ]
  },
  "rules": {
    "no-console": process.env.NODE_ENV === 'production' ? 2 : 0,
    "comma-dangle": [
      "error",
      {
        "arrays": "always-multiline",
        "objects": "always-multiline",
        "imports": "always-multiline",
        "exports": "always-multiline",
        "functions": "ignore"
      }
    ]
  }
}
