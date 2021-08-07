module.exports = {
  extends: ["plugin:vue/recommended"],
  rules: {
    "vue/max-attributes-per-line": [
      "error",
      {
        singleline: {
          max: 10,
          allowFirstLine: true,
        },
        multiline: {
          max: 1,
          allowFirstLine: false,
        },
      },
    ],
  },
};
