@echo off
set INPUT_DIR=public\storage\CourseLecture

for %%f in ("%INPUT_DIR%\*.mp4") do (
    echo Processing: %%~nxf
    ffmpeg -i "%%f" -c copy -movflags +faststart "%%f_fixed.mp4"
    move /Y "%%f_fixed.mp4" "%%f"
)

echo.
echo ✅ Done! All videos are now seekable.
pause
