<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //ให้ใส่ Token ตรงนี้
    $token = 'TOKEN';

    // เอาหมายเลขเครื่องซักผ้า
    if (isset($_POST['machine'])) {
        $machineNumber = $_POST['machine'];
    } else {
        $machineNumber = 'Unknown';
    }

    // ส่ง Line notify 
    $message = 'Machine ' . $machineNumber . ' timer is less than 1 min';
    $url = 'https://notify-api.line.me/api/notify';
    $data = array('message' => $message);
    $options = array(
            'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Bearer {$token}\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        echo 'Failed to send message to Line Notify';
    } else {
        echo 'Message sent to Line Notify';
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dudee Indeed</title>

    <style>
        .container {
            display: flex;
            position: relative;
            flex-direction: row;
        }

        .box {
            border: 1px solid black;
            padding: 10px;
            margin: 10px;
            width: 300px;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
        }

        .box img {
            max-width: 100%;
            height: auto;
        }

        .box button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .box button.countdown-active {
            background-color: red;
        }
    </style>

</head>

<h1>Line Notify with time counter</h1>
    <div class="container">
        <div class="box">
            <img src="https://res.cloudinary.com/cenergy-innovation-limited-head-office/image/fetch/c_scale,q_70,f_auto,h_740/https://d1dtruvuor2iuy.cloudfront.net/media/catalog/product/0/0/000262652_t.jpg" alt="Image">
            <button onclick="startCountdown(this, 'timer1', 120, 1)">Start</button>
            <p>เครื่อง 1</p>
            <p>Time: <span id="timer1">02:00</span></p>
        </div>
        <div class="box">
            <img src="https://res.cloudinary.com/cenergy-innovation-limited-head-office/image/fetch/c_scale,q_70,f_auto,h_740/https://d1dtruvuor2iuy.cloudfront.net/media/catalog/product/0/0/000262652_t.jpg" alt="Image">
            <button onclick="startCountdown(this, 'timer2', 120, 2)">Start</button>
            <p>เครื่อง 2</p>
            <p>Time: <span id="timer2">02:00</span></p>
        </div>
        <div class="box">
            <img src="https://res.cloudinary.com/cenergy-innovation-limited-head-office/image/fetch/c_scale,q_70,f_auto,h_740/https://d1dtruvuor2iuy.cloudfront.net/media/catalog/product/0/0/000262652_t.jpg" alt="Image">
            <button onclick="startCountdown(this, 'timer3', 120, 3)">Start</button>
            <p>เครื่อง 3</p>
            <p>Time: <span id="timer3">02:00</span></p>
        </div>
    </div>

    <script>
        function startCountdown(button, timerId, defaultTime, machineNumber) {
            button.disabled = true; // เวลากดปุ่มแล้วจะไม่สามารถกดปุ่มได้
            button.classList.add('countdown-active'); // เปลี่ยนสีตอนกด
            var duration = defaultTime; // เปลี่ยนเวลาให้เป็นวินาที
            var timer = document.getElementById(timerId);
            var intervalId = setInterval(function() {


                if (duration <= 60) {
                    // ส่งคำขอ Post ไปยัง php
                    if (duration === 60){
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', 'index.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                                // รับ response จาก PHP
                                var responseMessage = xhr.responseText;
                                console.log(responseMessage);
                            }
                        };
                        xhr.send('machine=' + machineNumber);
                    }
                    if (duration <= 0) {
                        clearInterval(intervalId);
                        button.disabled = false; // รีเซ็ทปุ่มเมื่อเสร็จสิ้น
                        button.classList.remove('countdown-active'); // นำสีออกจากปุ่ม
                        // รีเซ็ทเวลา
                        timer.textContent = formatTime(defaultTime);
                        return;
                    }
                }
                timer.textContent = formatTime(duration);
                duration--;
            }, 1000);
        }

        function formatTime(time) {
            var minutes = Math.floor(time / 60);
            var seconds = time % 60;
            return minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
        }
    </script>

</html>
