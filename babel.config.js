module.exports = {
  presets: [
    [
      "@babel/env",
      {
        useBuiltIns: "usage",
        corejs: "3.3",
        forceAllTransforms: true,
        targets: "ie 11, >0.25%, not dead"
      }
    ]
  ]
};