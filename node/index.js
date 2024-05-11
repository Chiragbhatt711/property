const express = require("express");
const concat = require("ffmpeg-concat");
const ffmpeg = require("fluent-ffmpeg");
const path = require("path");
const fs = require("fs");

function writeToLogFile(message) {
    const timestamp = new Date().toISOString();
    const logMessage = `${timestamp} - ${message}\n`;

    fs.appendFile("log.txt", logMessage, (err) => {
        if (err) {
            console.error(err);
        }
    });
}

const app = express();
app.use(express.json());
const port = 3000;

app.listen(port, () => {
    writeToLogFile(`Server running on port ${port}`);
});
const outputPath = path.join(__dirname, "output.mp4");
app.post("/mergeVideo", async (req, res) => {
    let __videoName = `${Date.now()}.mp4`;
    const { type, main_video, second_video } = req.body;
    switch (type) {
        case "anchors":
            var _main = path.join(
                __dirname,
                `../public/uploads/anchor_main_video/${main_video}`
            );
            var _second = path.join(
                __dirname,
                `../public/uploads/anchor_video/${second_video}`
            );
            var FileName = path.join(
                __dirname,
                `../public/uploads/anchor_main_video/${__videoName}`
            );
            break;
        case "ripples":
            var _main = path.join(
                __dirname,
                `../public/uploads/ripple_main_video/${main_video}`
            );
            var _second = path.join(
                __dirname,
                `../public/uploads/ripple_video/${second_video}`
            );
            var FileName = path.join(
                __dirname,
                `../public/uploads/ripple_main_video/${__videoName}`
            );
            break;
        default:
            break;
    }
    // process.env.DISPLAY = ":99";
    if (_main && _second) {
        ffmpeg()
            .input(_main)
            .outputOptions("-c:v", "libx264")
            .outputOptions("-preset", "slow")
            .outputOptions("-crf", "22")
            .outputOptions("-vf", "scale=1280:720")
            .output(path.join(__dirname, main_video))
            .on("end", () => {
                writeToLogFile(`${main_video} conversion finished`);
                ffmpeg()
                    .input(_second)
                    .outputOptions("-c:v", "libx264")
                    .outputOptions("-preset", "slow")
                    .outputOptions("-crf", "22")
                    .outputOptions("-vf", "scale=1280:720")
                    .output(path.join(__dirname, second_video))
                    .on("end", async () => {
                        writeToLogFile(`${second_video} conversion finished`);
                        // Merge the two mp4 files into output.mp4
                        try {
                            await concat({
                                output: FileName,
                                videos: [main_video, second_video],
                                transition: {
                                    name: "swap",
                                    duration: 500,
                                },
                            });
                            fs.unlink(
                                path.join(__dirname, main_video),
                                (err) => {
                                    writeToLogFile(
                                        `${main_video} deleted successfully`
                                    );
                                }
                            );
                            fs.unlink(
                                path.join(__dirname, second_video),
                                (err) => {
                                    writeToLogFile(
                                        `${second_video} deleted successfully`
                                    );
                                }
                            );
                            writeToLogFile("Done......");
                            res.status(200).send({
                                message: "Success",
                                data: __videoName,
                            });
                        } catch (error) {
                            writeToLogFile(error);
                            res.status(500).send("Error generating video");
                        }
                    })
                    .on("error", (err) => {
                        writeToLogFile("An error occurred 1: " + err.message);
                        res.status(404).send({
                            message: "Video not found",
                        });
                    })
                    .run();
            })
            .on("error", (err) => {
                writeToLogFile("An error occurred 2: " + err.message);
                res.status(404).send({
                    message: "Video not found",
                });
            })
            .run();
    } else {
        res.status(404).send({
            message: "Video not found",
        });
    }
});
