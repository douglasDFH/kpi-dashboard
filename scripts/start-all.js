#!/usr/bin/env node

import { spawn } from 'child_process';
import path from 'path';
import { fileURLToPath } from 'url';
import os from 'os';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const projectRoot = path.resolve(__dirname, '..');
const isWindows = os.platform() === 'win32';

console.log('\n🚀 Iniciando KPI Dashboard...\n');
console.log('📋 Servicios a iniciar:');
console.log('  1️⃣  Vite Dev Server (npm run dev)');
console.log('  2️⃣  Laravel Server (php artisan serve)');
console.log('  3️⃣  Queue Worker (php artisan queue:work)\n');

const processes = [];
let shouldExit = false;

// Función para terminar todos los procesos
function killAllProcesses(reason = '') {
  if (shouldExit) return;
  shouldExit = true;

  console.log('\n\n🛑 Deteniendo todos los servicios...');
  if (reason) console.log(`Razón: ${reason}\n`);

  processes.forEach((proc, index) => {
    try {
      if (proc && proc.pid) {
        process.kill(-proc.pid, 'SIGTERM');
      }
    } catch (e) {
      // El proceso ya finalizó
    }
  });

  setTimeout(() => {
    process.exit(1);
  }, 1000);
}

// Función para iniciar un proceso
function startProcess(label, command, args) {
  console.log(`⏳ Iniciando ${label}...`);

  let stderr = '';
  let stdout = '';

  // En Windows, usar .cmd para npm
  let cmd = command;
  let cmdArgs = args;
  
  if (isWindows && command === 'npm') {
    cmd = 'npm.cmd';
  }

  const child = spawn(cmd, cmdArgs, {
    cwd: projectRoot,
    stdio: ['inherit', 'pipe', 'pipe'],
    shell: isWindows,
  });

  // Capturar salida para logging
  if (child.stdout) {
    child.stdout.on('data', (data) => {
      stdout += data.toString();
      process.stdout.write(data);
    });
  }

  if (child.stderr) {
    child.stderr.on('data', (data) => {
      stderr += data.toString();
      process.stderr.write(data);
    });
  }

  child.on('error', (err) => {
    console.error(`\n❌ Error al iniciar ${label}:`, err.message);
    killAllProcesses(`Error en ${label}: ${err.message}`);
  });

  child.on('close', (code) => {
    if (code === 0) {
      console.log(`\n✅ ${label} finalizó correctamente`);
    } else if (code === 130 || code === null) {
      console.log(`\n⏹  ${label} fue detenido`);
    } else {
      console.error(`\n❌ ${label} finalizó con error (código: ${code})`);
      if (stderr) {
        console.error(`Error details: ${stderr.substring(0, 200)}`);
      }
      killAllProcesses(`${label} terminó con código ${code}`);
    }
  });

  processes.push(child);
}

// Iniciar los servicios
startProcess('Vite Dev Server', 'npm', ['run', 'dev']);
startProcess('Laravel Server', 'php', ['artisan', 'serve']);
startProcess('Queue Worker', 'php', ['artisan', 'queue:work']);

console.log('\n✅ Todos los servicios se están iniciando...\n');
console.log('📍 URLs:');
console.log('  • Frontend (Vite): http://localhost:5173');
console.log('  • Laravel API: http://localhost:8000\n');

console.log('⏸  Presiona Ctrl+C para detener todos los servicios.\n');

// Manejar Ctrl+C para terminar todos los procesos
process.on('SIGINT', () => {
  killAllProcesses('Usuario presionó Ctrl+C');
});

// Manejar otros señales
process.on('SIGTERM', () => {
  killAllProcesses('Señal SIGTERM recibida');
});
