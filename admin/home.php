<?php
include('../includes/common.php');
if ($islogin != 1) {
  exit('<script language=\'javascript\'>window.location.href=\'./login.php\';</script>');
}

$sec_msg = sec_check();
?>

<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <title>后台首页</title>
  <link rel="icon" href="favicon.ico" type="image/ico">
  <link href="../static/admin/css/materialdesignicons.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #2ECC71;
      --primary-dark: #27AE60;
      --bg-color: #f5f5f5;
      --text-color: #333;
      --card-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    body {
      background-color: var(--bg-color);
      color: var(--text-color);
      padding: 1.5rem;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .stat-card {
      background: white;
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: var(--card-shadow);
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: white;
    }

    .stat-info {
      flex: 1;
    }

    .stat-value {
      font-size: 1.5rem;
      font-weight: bold;
      margin-bottom: 0.25rem;
    }

    .stat-label {
      color: #666;
      font-size: 0.9rem;
    }

    .bg-primary { background-color: #3498db; }
    .bg-danger { background-color: #e74c3c; }
    .bg-success { background-color: #2ecc71; }
    .bg-purple { background-color: #9b59b6; }

    .main-content {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 1.5rem;
    }

    .card {
      background: white;
      border-radius: 8px;
      box-shadow: var(--card-shadow);
      margin-bottom: 1.5rem;
    }

    .card-header {
      padding: 1rem 1.5rem;
      border-bottom: 1px solid #eee;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .card-title {
      font-size: 1.1rem;
      font-weight: 500;
    }

    .card-body {
      padding: 1.5rem;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
    }

    .table th,
    .table td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    .table th {
      font-weight: 500;
      color: #666;
    }

    .table tr:hover {
      background-color: #fafafa;
    }

    .stats-grid-small {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1rem;
    }

    .stat-item {
      text-align: center;
      padding: 1rem;
    }

    .stat-item i {
      font-size: 1.5rem;
      color: var(--primary-color);
      margin-bottom: 0.5rem;
    }

    .stat-item .value {
      font-size: 1.25rem;
      font-weight: bold;
      margin-bottom: 0.25rem;
    }

    .stat-item .label {
      color: #666;
      font-size: 0.9rem;
    }

    .list-group {
      list-style: none;
    }

    .list-group-item {
      padding: 1rem 1.5rem;
      border-bottom: 1px solid #eee;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .list-group-item:last-child {
      border-bottom: none;
    }

    .btn-sm {
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-size: 0.875rem;
    }

    .btn-success {
      background-color: var(--primary-color);
      color: white;
    }

    @media (max-width: 1024px) {
      .main-content {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon bg-primary">
        <i class="mdi mdi-account"></i>
      </div>
      <div class="stat-info">
        <div class="stat-value" id="count1">0</div>
        <div class="stat-label">用户总数</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon bg-danger">
        <i class="mdi mdi-account-star"></i>
      </div>
      <div class="stat-info">
        <div class="stat-value" id="count2">0</div>
        <div class="stat-label">会员总数</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon bg-success">
        <i class="mdi mdi-multiplication-box"></i>
      </div>
      <div class="stat-info">
        <div class="stat-value" id="count3">0</div>
        <div class="stat-label">总订单数</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon bg-purple">
        <i class="mdi mdi-cash-usd"></i>
      </div>
      <div class="stat-info">
        <div class="stat-value" id="count4">$0</div>
        <div class="stat-label">总交易额</div>
      </div>
    </div>
  </div>

  <div class="main-content">
    <div class="left-content">
      <div class="card">
        <div class="card-header">
          <div class="card-title">近7日统计报表</div>
        </div>
        <div class="card-body">
          <canvas class="js-chartjs-lines"></canvas>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">热门网址</div>
        </div>
        <div class="card-body">
          <table class="table">
            <thead>
              <tr>
                <th>短网址</th>
                <th>访问次数</th>
                <th>生成时间</th>
                <th>最后访问</th>
              </tr>
            </thead>
            <tbody id="hot-url">
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="right-content">
      <div class="card">
        <div class="card-header">
          <div class="card-title">网址统计</div>
        </div>
        <div class="card-body">
          <div class="stats-grid-small">
            <div class="stat-item">
              <i class="mdi mdi-web"></i>
              <div class="value" id="count5">0</div>
              <div class="label">网址总数</div>
            </div>
            <div class="stat-item">
              <i class="mdi mdi-svg"></i>
              <div class="value" id="count6">0</div>
              <div class="label">普通跳转</div>
            </div>
            <div class="stat-item">
              <i class="mdi mdi-multiplication-box"></i>
              <div class="value" id="count7">0</div>
              <div class="label">防红跳转</div>
            </div>
            <div class="stat-item">
              <i class="mdi mdi-blender"></i>
              <div class="value" id="count8">0</div>
              <div class="label">直链防红</div>
            </div>
            <div class="stat-item">
              <i class="mdi mdi-wechat"></i>
              <div class="value" id="count9">0</div>
              <div class="label">微信监控</div>
            </div>
            <div class="stat-item">
              <i class="mdi mdi-qqchat"></i>
              <div class="value" id="count10">0</div>
              <div class="label">QQ监控</div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">站点提示</div>
        </div>
        <div class="card-body">
          <ul class="list-group">
            <?php
            foreach ($sec_msg as $row) {
              echo $row;
            }
            if (count($sec_msg) == 0) echo '<li class="list-group-item"><span class="btn-sm btn-success">正常</span>&nbsp;暂未发现网站安全问题</li>';
            ?>
          </ul>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title">版本更新</div>
        </div>
        <div class="card-body" id="checkupdate">
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript" src="../static/admin/js/jquery.min.js"></script>
  <script type="text/javascript" src="../static/admin/js/Chart.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#title').html('正在加载数据中...');
      $.ajax({
        type: "GET",
        url: "ajax.php?act=getcount",
        dataType: 'json',
        async: true,
        success: function(data) {
          $('#title').html('后台首页');
          $('#count1').html(data.count1);
          $('#count2').html(data.count2);
          $('#count3').html(data.count3);
          $('#count4').html('$' + data.count4);
          $('#count5').html(data.count5);
          $('#count6').html(data.count6);
          $('#count7').html(data.count7);
          $('#count8').html(data.count8);
          $('#count9').html(data.count9);
          $('#count10').html(data.count10);
          var $dashChartLinesCnt = jQuery('.js-chartjs-lines')[0].getContext('2d');
          var $dashChartLinesData = {
            labels: ['<?php echo getWeek(6); ?>', '<?php echo getWeek(5); ?>', '<?php echo getWeek(4); ?>', '<?php echo getWeek(3); ?>', '<?php echo getWeek(2); ?>', '<?php echo getWeek(1); ?>', '<?php echo getWeek(0); ?>'],
            datasets: [{
              label: '用户',
              data: [data.chart.user[6], data.chart.user[5], data.chart.user[4], data.chart.user[3], data.chart.user[2], data.chart.user[1], data.chart.user[0]],
              borderColor: '#358ed7',
              backgroundColor: 'rgba(53, 142, 215, 0.175)',
              borderWidth: 1,
              fill: false,
              lineTension: 0.5
            }, {
              label: '网址',
              data: [data.chart.url[6], data.chart.url[5], data.chart.url[4], data.chart.url[3], data.chart.url[2], data.chart.url[1], data.chart.url[0]],
              borderColor: '#FFD700',
              backgroundColor: 'rgba(255, 215, 0, 0.175)',
              borderWidth: 1,
              fill: false,
              lineTension: 0.5
            }, {
              label: '订单',
              data: [data.chart.order[6], data.chart.order[5], data.chart.order[4], data.chart.order[3], data.chart.order[2], data.chart.order[1], data.chart.order[0]],
              borderColor: '#E066FF',
              backgroundColor: 'rgba(224, 102, 255, 0.175)',
              borderWidth: 1,
              fill: false,
              lineTension: 0.5
            }, {
              label: '交易额',
              data: [data.chart.money[6], data.chart.money[5], data.chart.money[4], data.chart.money[3], data.chart.money[2], data.chart.money[1], data.chart.money[0]],
              borderColor: '#C0FF3E',
              backgroundColor: 'rgba(192, 255, 62, 0.175)',
              borderWidth: 1,
              fill: false,
              lineTension: 0.5
            }, {
              label: '监控',
              data: [data.chart.check[6], data.chart.check[5], data.chart.check[4], data.chart.check[3], data.chart.check[2], data.chart.check[1], data.chart.check[0]],
              borderColor: '#FFCCCC',
              backgroundColor: 'rgba(255, 204, 204, 0.175)',
              borderWidth: 1,
              fill: false,
              lineTension: 0.5
            }],
          };

          var myLineChart = new Chart($dashChartLinesCnt, {
            type: 'line',
            data: $dashChartLinesData,
          });

          $.ajax({
            url: 'ajax.php?act=update',
            type: 'get',
            dataType: 'json',
            jsonpCallback: 'callback'
          }).done(function(data) {
            $("#checkupdate").html(data.msg);
          })

          $.ajax({
            url: 'ajax.php?act=getHotUrl',
            type: 'get',
            dataType: 'json',
            jsonpCallback: 'callback'
          }).done(function(data) {
            var rows = data.rows
            for (i = 0, len = rows.length; i < len; i++) {
              $('#hot-url').append('<tr><td>' + rows[i].dwz + '</td><td>' + rows[i].view + '</td><td>' + rows[i].addtime + '</td><td>' + rows[i].lasttime + '</td></tr>')
            }
          })
        }
      });
    })
  </script>
</body>

</html>