<?php

return [
    'extract_metadata' => env("ANALYSER_EXTRACT_METADATA_URL", "http://qanun_api_analyser:8001/dataset/extract-metadata/"),
    'line_chart' => env("ANALYSER_LINE_CHART_URL", "http://qanun_api_analyser:8001/dataset/line-chart/"),
    'scatter_chart' => env("ANALYSER_SCATTER_CHART_URL", "http://qanun_api_analyser:8001/dataset/scatter-chart/"),
    'histogram_chart' => env("ANALYSER_HISTOGRAM_CHART_URL", "http://qanun_api_analyser:8001/dataset/histogram-chart/"),
];
