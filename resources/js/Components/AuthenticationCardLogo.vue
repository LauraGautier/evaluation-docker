<template>
    <div class="logo-container">
      <img :src="logo" alt="Logo" width="40%" class="circular-logo">
    </div>
  </template>

  <script setup>
  import logo from '../../images/logo.png';
  </script>

  <style scoped>
  .logo-container {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .circular-logo {
    border-radius: 50%;
    object-fit: cover;
  }
  </style>
