# Proyecto-Residencias-Tutoria-TESCHA
# Cómo trabajar con este repositorio 🚀

Esta guía explica, paso a paso, cómo usar Git y GitHub para colaborar en este proyecto. No necesitas ser experto, solo sigue estos pasos en orden.

## Antes de empezar

Necesitas tener instalado:
- [Git](https://git-scm.com/downloads)
- Una cuenta de GitHub (y que te hayan agregado como colaborador del repositorio)

Para comprobar que Git está instalado, abre una terminal y escribe:

```bash
git --version
```

## 1. Clonar el repositorio (solo la primera vez)

Copia el repositorio a tu computadora:

```bash
git clone <URL-del-repositorio>
```

La URL la encuentras en GitHub, en el botón verde **Code**.

Luego entra a la carpeta:

```bash
cd nombre-del-repositorio
```

## 2. Antes de empezar a trabajar cada día

Trae los últimos cambios que hayan subido tus compañeros:

```bash
git pull
```

⚠️ Hazlo siempre antes de empezar a editar, para evitar conflictos.

## 3. Hacer cambios

Edita los archivos que necesites con tu editor de código de siempre.

## 4. Guardar tus cambios (commit)

Cuando termines una parte de tu trabajo:

```bash
git add .
git commit -m "Descripción breve de lo que hiciste"
```

Ejemplo de buen mensaje: `"Agrega formulario de login"`

## 5. Subir tus cambios a GitHub

```bash
git push
```

Ahora tus compañeros pueden ver y descargar tus cambios.

## 6. (Recomendado) Trabajar con ramas

Para evitar chocar con el trabajo de otros, crea tu propia rama antes de editar:

```bash
git checkout -b nombre-de-mi-rama
```

Trabaja ahí normalmente (add, commit, push). Cuando termines, ve a GitHub y crea un **Pull Request** para unir tu rama con la principal (`main`). Alguien del equipo puede revisar el cambio antes de aprobarlo.

## 7. Si aparece un conflicto

Git te avisará algo como `CONFLICT`. Esto pasa cuando dos personas editaron la misma línea. Para resolverlo:

1. Abre el archivo marcado en conflicto.
2. Verás algo así:
   ```
   <<<<<<< HEAD
   tu código
   =======
   código de tu compañero
   >>>>>>> main
   ```
3. Decide qué parte quedarte (o combina ambas) y borra las líneas `<<<<<<<`, `=======`, `>>>>>>>`.
4. Guarda el archivo y vuelve a hacer:
   ```bash
   git add .
   git commit -m "Resuelve conflicto"
   git push
   ```

## Resumen rápido (chuleta) 📝

| Quiero...                        | Comando                          |
|----------------------------------|-----------------------------------|
| Descargar el repo por primera vez| `git clone <url>`                |
| Traer cambios nuevos              | `git pull`                        |
| Ver qué archivos cambié           | `git status`                      |
| Guardar mis cambios               | `git add .` + `git commit -m "..."` |
| Subir mis cambios                 | `git push`                        |
| Crear una rama nueva              | `git checkout -b mi-rama`         |
| Cambiar de rama                   | `git checkout nombre-rama`        |

## Regla de oro

**Pull antes de empezar, push al terminar.** Así evitamos casi todos los problemas. Si tienes dudas, pregunta en el chat del equipo antes de forzar algo (`git push --force`) — nunca lo uses sin avisar.
