#!/bin/bash
# Upload static HTML to S3 bucket
S3_BUCKET="readsmart-static-site"
aws s3 cp ./html/ s3://$S3_BUCKET/ --recursive
echo "HTML site uploaded to S3: $S3_BUCKET"
