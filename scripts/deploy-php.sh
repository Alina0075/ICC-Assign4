#!/bin/bash
# Deploy PHP code to private EC2 instance
EC2_INSTANCE_ID="i-0123456789abcdef0"  # Update in real scenario
PHP_SOURCE_DIR="./php/"

# Using SSM to copy files (EC2 must have IAM Role with ssm:SendCommand & s3:GetObject)
aws ssm send-command \
    --targets "Key=instanceIds,Values=$EC2_INSTANCE_ID" \
    --document-name "AWS-RunShellScript" \
    --comment "Deploy PHP backend" \
    --parameters 'commands=["mkdir -p /var/www/html","aws s3 cp s3://readsmart-static-site/php/ /var/www/html/ --recursive","systemctl restart httpd"]'
echo "PHP code deployment triggered via SSM"
